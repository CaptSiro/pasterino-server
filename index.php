<?php

require_once __DIR__ . "/lib/import.php";
require_once __DIR__ . "/constants.php";


import("routepass");
$router = HomeRouter::getInstance();


$router->setBodyParser(HomeRouter::BODY_PARSER_JSON());
$router->setViewDirectory(__DIR__ . "/views");
$router->static("/public", __DIR__ . "/public");


$router->get("/", [function (Request $request, Response $response) {
    import("dotenv");
    $env = new Env(ENV);

    $response->render("pasterino", [
        "project_path" => $env->get_or_crash("ORIGIN")
    ]);
}]);


$router->use("/auth", new RouterPromise(__DIR__ . "/routes/auth-router.php"));
$router->use("/copy-pasta", new RouterPromise(__DIR__ . "/routes/copy-pasta-router.php"));
$router->use("/user", new RouterPromise(__DIR__ . "/routes/user-router.php"));


$router->serve();