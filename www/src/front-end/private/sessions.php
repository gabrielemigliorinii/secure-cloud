<?php

    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    require_once '../../back-end/class/system.php';

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
                            header("Location: ../public/otp.php");
                            exit;
                        }

                        // START SESSION
                        {

                            // SESSION IS ACTIVE, UPDATE LAST ACTIVITY
                            if (isset($_SESSION['SESSION_STATUS_ACTIVE']))
                            {
                                sqlc::connect("USER_STD_UPD");
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                sqlc::upd_session($session_sc_id);
                                sqlc::close();
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
                                    sqlc::upd_session($session_sc_id);
                                }
                                else
                                {
                                    // CREATE NEW SESSION
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
                    else header("Location: ../public/signin.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }
                else header("Location: ../public/signin.php");

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


?>

<!------ START BOOTSTRAP FORM ---------->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="cloud.php">CLOUD</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="storage.php">Storage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="transfers.php">Transfers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page" href="sessions.php">Sessions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="settings.php">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page"></a>
                        </li>
                        <button onclick="window.location.href='../../back-end/class/out.php'" class="btn btn-dark btn-secondary">
                          <i class="fa fa-sign-out"></i>
                          <span>Logout</span>
                        </button>
                    </ul>
                </div>
            </div>
        </nav>

        <br><br>

        <table class="table table-dark tbls" id="ID_SESSIONS">
            <thead>
                <tr class="chd">
                    <th scope="col">IP Address</th>
                    <th scope="col">Client</th>
                    <th scope="col">OS</th>
                    <th scope="col">Device</th>
                    <th scope="col">Last activity</th>
                    <th scope="col">Session status</th>
                    <th scope="col">Edit</th>
                </tr>
            </thead>
            <tbody id="ID_TBL_BODY">
            </tbody>
        </table>
        <br><br>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">

    "use strict";

    import Polling from "../class/polling.js";
    import {checkpk} from "../class/pvtk.js"

    var SESSION_SC_ID;
    var getSessionStatus;

    $('document').ready(() => {
        checkpk()
        getSessionStatus = new Polling(sessionStatus, 5000);
        getSessionStatus.Start();
        syncSessions();
    });

    const sessionStatus = () => {
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSION_ID:SESSION_SC_ID},
            type: "GET",
            success: (response) => {
                console.info("session status "+response);
                if (response == 0)
                {
                    getSessionStatus.Stop();
                    alert("Sessione terminata, clicca ok per continuare");
                    window.location.href = "../../back-end/class/out.php"
                }
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    }

    const closeSession = (idRowBtn, idRowTxt, sessionID) => {
        getSessionStatus.Stop();

        if ($('#'+idRowTxt).html() === "Actual")
        {
            if (confirm("Stai per terminare la session attuale"))
            {
                window.location.href = "../../back-end/class/out.php"
            }
            else
            {
                getSessionStatus.Start();
                return;
            }
        }

        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSION_ID: sessionID},
            type: "POST",
            success: (response) => {
                console.log(response);
                const del = (idRowBtn, idRowTxt) => {$('#'+idRowBtn).children().remove();$('#'+idRowTxt).html("Expired");}
                del(idRowBtn, idRowTxt);
                getSessionStatus.Start();
            },
            error: (xhr) => {
                console.log(xhr);
            } 
        })
    }

    const addSession = (idRow, sd) => {
        var rowHTML = "";
        sd.session_status = sd.session_status? sd.id === SESSION_SC_ID ? 'Actual' : 'Active': 'Expired';
        rowHTML += "<tr>";
            rowHTML += "<td>"+sd.ip+"</td>";
            rowHTML += "<td>"+sd.client+"</td>";
            rowHTML += "<td>"+sd.os+"</td>";
            rowHTML += "<td>"+sd.device+"</td>";
            rowHTML += "<td>"+sd.last_time+"</td>";
            rowHTML += "<td id='ROW_T_"+idRow+"'>"+sd.session_status+"</td>";
            if (sd.session_status !== "Expired") {
                rowHTML += "<td id='ROW_S_"+idRow+"'><button style='background-color:#212529;color:white;border:2px solid white;border-radius:4px' id='BTN_S_"+idRow+"'>Close</button></td>";
            }
            else rowHTML += "<td></td>";
        rowHTML += "</tr>";
        document.getElementById("ID_TBL_BODY").innerHTML += rowHTML;
    }

    const syncSessions = () => {

        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSIONS_DATA:true},
            type: "GET",
            success: (response) => {
                console.log(response);
                SESSION_SC_ID = "<?php echo $_SESSION['SESSION_SC_ID']; ?>";
                for (let i=0; i<response.sessions.length; i++){
                    addSession(i, response.sessions[i]);
                }
                for (let i=0; i<response.sessions.length; i++){
                    $('#BTN_S_'+i).on('click', () => closeSession("ROW_S_"+i, "ROW_T_"+i, response.sessions[i].id))
                }
            },
            error: (xhr) => {
                console.log(xhr);
            } 
        });
    }


    
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

    .tbls {

        margin-left: auto;
        margin-right: auto;
        width: 80%;
    }

    .chd {

        color: #30D2F2;
    }

</style>