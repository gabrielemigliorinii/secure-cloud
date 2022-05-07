<?php

    require_once 'backend-dir.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';
    require_once __BACKEND__ . 'class/system.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID']))
                {
                    session_start();
                    if (isset($_SESSION['ID_USER']) && isset($_SESSION['AUTH']))
                    {
                        if (isset($_SESSION['HOTP']))
                        {
                            header("Location: otp-form.php");
                            exit;
                        }

                        // START SESSION
                        {
                            sqlc::connect();

                            // SESSION IS ACTIVE, UPDATE LAST ACTIVITY
                            if (isset($_SESSION['SESSION_STATUS_ACTIVE']))
                            {
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                sqlc::upd_session($session_sc_id);
                            }
                            else
                            // remember me token setted
                            if (isset($_COOKIE['rm_tkn']))
                            {
                                $session = sqlc::sel_session("HTKN", $_COOKIE['rm_tkn']);
                                if ($session)
                                {
                                    // RESUME
                                    $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
                                    $_SESSION['SESSION_SC_ID'] = $session['id'];
                                    $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                    sqlc::upd_session($session_sc_id);
                                }
                                else
                                {
                                    // CREATE
                                    create_session();
                                }
                            }
                            // CREATE NEW SESSION
                            else 
                            {
                                create_session();
                            }

                            $email = sqlc::get_email($_SESSION['ID_USER']);
                        }

                    }
                    else header("Location: log.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }
                else header("Location: log.php");

                break;
            }

            case 'POST': {

                break;  
            }

            default: {

                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);

    function create_session()
    {
        $session_id = new token(16, "", "", array("0-9", "a-z"));
        $session_id = $session_id->val();
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $htkn = isset($_COOKIE['rm_tkn']) ? $_COOKIE['rm_tkn'] : null;
        sqlc::add_session($session_id, $http_user_agent, $ip, $_SESSION['ID_USER'], $htkn); 
        $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
        $_SESSION['SESSION_SC_ID'] = $session_id;
    }


?>

<!------ START BOOTSTRAP FORM ---------->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>


        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><?php echo $email; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../back-end/class/out.php">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#" id="ID_UPLOAD">Upload file</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="session_history.php" id="ID_UPLOAD">Cronologia sessioni</a>
                        </li>
                        <li class="nav-item">
                            <input id="OTP_YN" type="checkbox">
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <input type="file" id="ID_FILE_UPLOADER" style="display:none">
        <br><br><div id="C_FILES" class="FILE_CARDS"></div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">
    
    import CLIENT_FILE from '../class/clientfile.js'
    import cryptolib from '../class/cryptolib.js'
    import FILE_URL from '../class/blob.js'
    
    $('document').ready(() => {
        checkKey();
        sync_2FA_state();
        getData();
    })

    const getData = () => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {DATA:true},
            success: (response) => {
                console.log(response);
                const files = response.files;
                const rep = response.rep;
                var a = new cryptolib['AES'](localStorage.getItem("k"));
                response.files.forEach((filename) => {
                    if (filename !== "." && filename !== "..")
                        getFile(filename, rep, a)
                });
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }
    
    const getFile = (filename, rep, aes) => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {FILE:filename,REP:rep},
            success: (response) => {
                console.log(response);
                filename = filename.replaceAll("_", "/");
                var ctx = response.ctx;
                var [fn, url, blob] = GET_FILE_EXE(filename, ctx, aes);
                createVisualObj(url, fn);
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

    const checkKey = () => {
        const k = localStorage.getItem("k");
        if (k === null){
            alert("Chiave mancante, e' necessario riaccedere");
            window.location.href = "../../back-end/class/out.php";
            return;
        };
    }

    $("#ID_FILE_UPLOADER").on('change', async (e) => {
        
        checkKey(); 

        // upload file
        const file = {
            inf: e.target.files[0],
            ctx: await CLIENT_FILE.TO_BASE64(e.target.files[0]).catch((error) => console.log("Error uploading your file."))
        }

        const fobj = new CLIENT_FILE(file.inf, file.ctx)
        var aes = new cryptolib['AES'](localStorage.getItem("k"));
        const hash = cryptolib['HASH'].SHA256;
        const [NAM, CTX, IMP, SIZ] = fobj.ENCRYPT(aes, hash);
        var [fn, url, blob] = GET_FILE_EXE(NAM, CTX, aes);

        await createVisualObj(url, e.target.files[0].name);

        $.ajax({
            type: 'POST',
            url: "../../back-end/class/client_resource_handler.php",
            data: {NAM:NAM, CTX:CTX, IMP:IMP, SIZ:SIZ},
            success: (response) => {
                console.log(response);
                $("#ID_FILE_UPLOADER").val("");
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    });

    const GET_FILE_EXE = (NAM, CTX, aes) => {
        NAM = aes.decrypt(NAM, true);
        CTX = aes.decrypt(CTX, true);
        const BLOB_OBJ = FILE_URL.B64_2_BLOB(CTX);
        const BLOB_URL = FILE_URL.GET_BLOB_URL(BLOB_OBJ);
        return [NAM, BLOB_URL, BLOB_OBJ]
    }
    
    const createVisualObj = async (blob_url, filename) => {
        document.getElementById("C_FILES").innerHTML += '<br><br><a href='+blob_url+' download='+filename+'>'+filename+'</a><br><br>';
    }

    $('#OTP_YN').on('change', () => {
        const otp = $('#OTP_YN').prop('checked')? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "../../back-end/class/client_resource_handler.php",
            data: {OTP:otp},
            success: (response) => {
                console.log(response);
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    })

    const sync_2FA_state = () => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {OTPSTATE:1},
            success: (response) => {
                const val = response["2FA"];
                if (val === 1) $('#OTP_YN').prop('checked', true)     
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    }

    $("#ID_UPLOAD").on('click', () => {
        $('#ID_FILE_UPLOADER').trigger('click');
    });

</script>

<style>

    .FILE_CARDS {

        border: 2px solid white;
        border-radius: 25px;
        width: 80%;
        color: white;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    a, h1, h3 {

        color: white;
    }

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

</style>