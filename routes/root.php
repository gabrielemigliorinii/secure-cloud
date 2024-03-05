<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../core/controller/static_pages.php';
    require_once __DIR__ . '/../core/controller/auth.php';

    abstract class root implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/', [], function () {

                AuthController::checkSignedIn();
                StaticPagesController::renderPage('home');
            });

            return $router->getRoutes();
        }
    }

?>