<?php

  return function (string $methods = "GET, HEAD, POST, PUT, PATCH, DELETE"): Closure {
    return function (Request $request, Response $response, Closure $next) use ($methods) {
      $response->setHeader("Access-Control-Request-Method", $methods);
      $response->setHeader("Access-Control-Request-Headers", "access-control-allow-origin");
      $response->setHeader("Access-Control-Allow-Credentials", "true");
      $response->setHeader("Access-Control-Allow-Origin", "https://www.twitch.tv");
      $next();
    };
  };