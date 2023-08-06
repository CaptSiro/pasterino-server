<?php

import("routepass", "routers");
import("oakbase");
import("dotenv");

import_model("State");
import_model("Session");
import_model("User");

$ext_accessible = import_middleware("ext-accessible");
$ext_accessible_get = $ext_accessible("GET");

use models\Session;
use models\State;
use models\User;
use OakBase\SideEffect;
use function OakBase\param;

$auth_router = new Router();


$config_db = import_middleware("configure-database");


$auth_router->get("/state", [
    $config_db,
    function (Request $request, Response $response) {
        $state = State::create(param($request->query->looselyGet("r", "https://www.twitch.tv/btmc")))
            ->forwardFailure($response);

        $response->json(["state" => $state->getSuccess()]);
    }
]);


$auth_router->get("/login", [function (Request $request, Response $response) {
    $env = new Env(ENV);

    $response->render("login", [
        "project_path" => $env->get_or_crash("ORIGIN")
    ]);
}]);


$auth_router->post("/session", [
    $config_db,
    function (Request $request, Response $response) {
        $id = param($request->body->get("id"));
        $state = param($request->body->get("state"));

        $redirect = State::by_state($state)
            ->forwardFailure($response)
            ->getSuccess()
            ->redirect;

        User::by_id($id)
            ->forwardFailure($response);

        Session::create($state, $id)
            ->forwardFailure($response);

        $response->json([
            "redirect" => $redirect
        ]);
    }
]);


$auth_router->get("/process-twitch-auth", [function (Request $request, Response $response) {
    $env = new Env(ENV);

    $response->render("process-twitch-auth", [
        "origin" => $env->get_or_crash("ORIGIN")
    ]);
}]);


$auth_router->post("/register", [
    $config_db,
    function (Request $request, Response $response) {
        var_dump("oi?");
        $username = param($request->body->get("username"));
        $pfp = param($request->body->looselyGet("profile_picture"));
        $id = param($request->body->get("id"));
        $state = param($request->body->get("state"));

        $exists = State::by_state($state)
            ->forwardFailure($response);

        if ($exists->getSuccess() === false) {
            $response->fail(new NotFoundExc("Unknown state"));
            return;
        }

        /** @var SideEffect $sideEffect */
        $sideEffect = User::create($id, $username, $pfp)
            ->forwardFailure($response)
            ->getSuccess();

        $response->setStatusCode(Response::CREATED);
        $response->json(
            Session::create($state, param($sideEffect->last_inserted_ID()))
                ->forwardFailure($response)
        );
    }
]);


$auth_router->options("/set-cookie", [$ext_accessible_get]);
$auth_router->get("/set-cookie", [
    $ext_accessible_get,
    $config_db,
    function (Request $request, Response $response) {
        $name = SESSION_COOKIE;
        $state = $request->query->get("s");

        Session::by_id(param($state))
            ->forwardFailure($response);

        $expires = Session::refresh(param($state))
            ->forwardFailure($response)
            ->getSuccess();

        $response->setHeader("Set-Cookie", "$name=$state; Path=/; SameSite=None; Secure; Expires=$expires");

        $response->json(["status" => "ok"]);
    }
]);


return $auth_router;