<?php

  function import(string $lib, string $module = null): void {
    if ($module !== null) {
      require_once __DIR__ . "/$lib/$module.php";
      return;
    }
    
    require_once __DIR__ . "/$lib/$lib.php";
  }
  
  function import_model(string $model): void {
    require_once __DIR__ . "/../models/$model.php";
  }
  
  function import_middleware(string $middleware): Closure {
    return require __DIR__ . "/../middleware/$middleware.php";
  }