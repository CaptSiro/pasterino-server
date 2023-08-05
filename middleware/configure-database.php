<?php
  
  require_once __DIR__ . "/../lib/dotenv/dotenv.php";
  $env = new Env(__DIR__ . "/.env");
  
  require_once __DIR__ . "/../lib/oakbase/oakbase.php";
  use OakBase\Database;
  use OakBase\BasicConfig;

  return function (Request $request, Response $response, Closure $next) use ($env) {
    Database::configure(new BasicConfig(
      $env->get_or_crash("DB_HOST"),
      $env->get_or_crash("DB_NAME"),
      $env->get_or_crash("DB_USER"),
      $env->get_or_crash("DB_PASSWORD"),
      $env->get_or_crash("DB_PORT")
    ));
    
    $next();
  };