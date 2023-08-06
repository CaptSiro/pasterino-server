<?php

require_once __DIR__ . "/lib/import.php";
require_once __DIR__ . "/constants.php";


const CODE_CHARS = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789-_";
/**
 * @throws Exception
 */
function codeGen($length = 64): string {
    $code = "";
    for ($i = 0; $i < $length; $i++) {
        $code .= CODE_CHARS[random_int(0, 63)];
    }
    return $code;
}

function getDatabase(): stdClass {
    return json_decode(file_get_contents(DB));
}

function saveDatabase($db) {
    file_put_contents(DB, json_encode($db));
}


import("routepass");
$router = HomeRouter::getInstance();

const DB = __DIR__ . "/db.json";


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