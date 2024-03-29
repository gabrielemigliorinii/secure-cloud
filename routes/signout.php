<?php

    require_once __DIR__ . '/routes_interface.php';

    abstract class signout implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();
            $router->GET('/signout', [], function() {

                AuthController::checkProtectedArea();
                SigninController::processSignout();
            });

            return $router->getRoutes();
        }
    }

?>