<?php

    require_once "response.php";
    require_once "sqlc.php";

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
            {
                if (isset($_GET['SESSIONS_DATA']) && count($_GET) === 1)
                {
                    session_start();
                    sqlc::connect("USER_STD_SEL");
                    $id_user = $_SESSION['ID_USER'];
                    $sessions = sqlc::sel_session_all(intval($id_user));
                    sqlc::close();
                    response::successful(200, false, array("sessions" => $sessions));
                    exit;
                }
                else if (isset($_GET['SESSION_ID']) && count($_GET) === 1)
                {
                    sqlc::connect("USER_STD_SEL");
                    $sstatus = sqlc::sel_session_status($_GET['SESSION_ID']);
                    sqlc::close();
                    response::light(200, $sstatus);
                }
                else response::client_error(400);

                break;
            }
            case 'POST':
            {
                if (isset($_POST['SESSION_ID']) && count($_POST) === 1)
                {
                    $session_id = $_POST['SESSION_ID'];
                    sqlc::connect("USER_STD_UPD");
                    if (sqlc::erase_session($session_id))
                        response::successful(200);
                    else
                        response::server_error(500, "Session already expired");
                    exit;
                }
                break;
            }
            default:
            {
                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);
?>