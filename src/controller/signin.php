<?php


    require_once __DIR__ . '/../../utils/httpkit/http_response.php';
    require_once __DIR__ . '/../../utils/httpkit/client.php';
    require_once __DIR__ . '/../../utils/securekit/crypto.php';
    require_once __DIR__ . '/../../utils/mypdo.php';
    require_once __DIR__ . '/../../utils/mydatetime.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/user_secrets.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    
    class SigninController
    {
        public static function renderSigninPage($success_msg = "", $error_msg = "")
        {
            $navbar = Navbar::getPublic('signin');
            include __DIR__ . "/../view/signin.php";
        }

        public static function processSignin($email, $pwd, $keepsigned)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                httpResponse::clientError(400, "Invalid email format");


            
            // ------------ BEGIN User PROCESS -----------

            $user = new UserModel(email:$email);
            
            // There's no email in db that is equals to $user->get_email()
            if ($user->sel_userID_by_email() === -1)
                httpResponse::clientError(400, "That email doesn't exists in our system");

            // ------------ END User PROCESS -----------



            // ------------ BEGIN UserSecrets PROCESS -----------

            $us = new UserSecretsModel(id_user: $user->getUserID());
            
            // there's no record in user_security that has that id_user, server error 
            if (!$us->sel_pwdHash_by_userID())
                httpResponse::serverError(500, "Something wrong, try again");

            // password is wrong (1FA FAILED)
            if (!password_verify($pwd, $us->getPasswordHash()))
                httpResponse::clientError(400, "Password is wrong");

            // ------------ END UserSecrets PROCESS -----------

            


            // ------------ BEGIN EmailVerify PROCESS -----------

            session_start();

            $id_user = $user->sel_verified_by_userID();

            if ($id_user === false || $id_user === null)
            {
                session_destroy();
                httpResponse::serverError();
            }

            // user is tryin' to signin without have verified the email 
            else if ($id_user === 0)
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'SIGNIN_WITH_EMAIL_NOT_VERIFIED';
                $_SESSION['EMAIL'] = $user->getEmail();
                
                httpResponse::clientError
                (
                    400, 
                    "Confirm your email before sign in", 
                    array("redirect" => '/verify')
                );
            }

            // user is verified
            else
            {
                $_SESSION['AUTH_1FA'] = true;
                $_SESSION['ID_USER'] = $user->getUserID();

                if (isset($_SESSION['VERIFY_PAGE_STATUS'])) 
                    unset($_SESSION['VERIFY_PAGE_STATUS']);
            }




            // ------------ END EmailVerify PROCESS -----------





            // ------------ BEGIN 2FA PROCESS -----------


            // check if 2FA isset

            if ($user->sel_2FA_by_userID() === null)
            {
                session_destroy();
                httpResponse::serverError(500);
            }

            // Set MASTER_KEY (a key derived from password and a random salt) to a session variable

            $us->setUserID($user->getUserID());
            $masterkey_salt = $us->sel_mKeySalt_by_userID();
            $_SESSION['MASTER_KEY'] = Crypto::deriveKey($pwd, $masterkey_salt);

            // If the user has 2FA active, redirect to 2FA page
            if ($user->get2FA() === 1)
            {
                $_SESSION['OTP_CHECKING'] = true;
                $_SESSION['KEEP_SIGNED'] = $keepsigned;

                httpResponse::successful
                (
                    200, 
                    false, 
                    array("redirect" =>  '/auth2')
                );
            }

            // No 2FA, login ok

            // ------------ END 2FA PROCESS -----------

            SessionController::initSession
            (
                Client::getInfo(), 
                $user->getUserID(), 
                $keepsigned
            );

            httpResponse::successful
            (
                200, 
                false, 
                array("redirect" =>  '/clouddrive')
            );
        }

        public static function processSignout()
        {
            if (session_status() == PHP_SESSION_NONE)                
                session_start();

            $session = new SessionModel(session_token: $_SESSION['SESSION_TOKEN']);
            $session->expire_by_sessionToken();
            unset($session);

            $_SESSION = [];
            session_destroy();

            if (isset($_COOKIE['session_token']))
                setcookie('session_token', "", time()-3600, '/');

            httpResponse::redirect('/signin');
        }
    }

?>