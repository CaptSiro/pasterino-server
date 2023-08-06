<?php
  
  import("dotenv");
  import("oakbase");
  
  use OakBase\Database;
  use OakBase\BasicConfig;

  
  
  $env = new Env(ENV);
  
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