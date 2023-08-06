<?php

use models\User;
use function OakBase\param;

import("routepass", "routers");
import("oakbase");

import_model("State");
import_model("User");

$user_router = new Router();

$config_db = import_middleware("configure-database");



// id=<user_id>
$user_router->get("/exists", [
    $config_db,
    function (Request $request, Response $response) {
        $token = param(intval($request->query->get("id")));

        $exists = User::by_id($token)
            ->isSuccess();

        $response->json([
            "exists" => $exists
        ]);
    }
]);



return $user_router;