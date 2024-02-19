<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/sessions.php';

    function get_sessions_routes()
    {
        $router = new Router();

        $router->GET('/sessions', [], function() {

            AuthController::check_protectedarea();
            SessionController::render_sessions_page();
        });

        $router->POST('/sessions/expire', ['id_session'], function($args) {

            AuthController::check_protectedarea(false);
            SessionController::expire_session($args['id_session']);
        });

        return $router->getRoutes();
    }

?>