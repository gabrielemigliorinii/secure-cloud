<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/signup.php';

    abstract class signup implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/signup', [], function() {
                
                AuthController::checkSignedIn();
                SignupController::renderSignupPage();
            });
        
            $router->GET('/signup/success', [], function() {
                
                AuthController::checkSignedIn();
                StaticPagesController::renderPage('signup_success');
            });
    
            $router->POST('/signup', ['email', 'pwd', 'name', 'surname'], function($args) {
            
                SignupController::processSignup
                (
                    $args['email'], 
                    $args['pwd'], 
                    $args['name'], 
                    $args['surname']
                );
            });
    
            return $router->getRoutes();
        }
    }

?>