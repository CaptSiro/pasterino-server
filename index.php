<?php
  
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
  
  
  
  /** @var HomeRouter $router */
  $router = require __DIR__ . "/lib/routepass/routepass.php";
  const DB = __DIR__ . "/db.json";
  
  
  
  $router->setBodyParser(HomeRouter::BODY_PARSER_JSON());
  $router->setViewDirectory(__DIR__ . "/views");
  
  
  
  $router->get("/", [function (Request $request, Response $response) {
    $response->render("get-cookie");
  }]);
  
  
  
  $router->get("/gen-code", [function (Request $request, Response $response) {
    $code = codeGen();
    $db = getDatabase();
    
    $db->codes[] = $code;
    
    saveDatabase($db);
    $response->send($code);
  }]);
  
  
  
  $router->forAll("/auth", [function (Request $request, Response $response) {
    $response->render("extract-token");
  }]);
  
  
  
  $router->options("view-cookies", [function (Request $request, Response $response) {
    $response->setCORS("POST");
    $response->setHeader(Response::HEADER_CORS_ORIGIN, "https://www.twitch.tv");
  }]);
  
  $router->post("view-cookies", [function (Request $request, Response $response) {
    $response->setCORS("POST");
    $response->setHeader(Response::HEADER_CORS_ORIGIN, "https://www.twitch.tv");
    $response->setHeader("Set-Cookie", "pasterino-server=". codeGen() ."; SameSite=None; Secure");
    $response->send(json_encode($_COOKIE));
  }]);
  
  
  
  $router->post("/create-user", [function (Request $request, Response $response) {
    $db = getDatabase();
    $state = $request->body->get("state");
    
    $index = array_search($state, $db->codes);
    
    if ($index === false) {
      $response->setStatusCode(Response::BAD_REQUEST);
      $response->flush();
      return;
    }
    
    array_splice($db->codes, $index, 1);
    
    $user = new stdClass();
    $user->state = $state;
    $user->token = $request->body->get("access_token");
    
    if (!isset($db->loggedIn)) {
      $db->loggedIn = [$user];
    } else {
      $db->loggedIn[] = $user;
    }
    
    saveDatabase($db);
    
    $response->setStatusCode(Response::CREATED);
  }]);
  
  
  
  $router->get("/cookie-redirect", [function (Request $request, Response $response) {
    $response->setHeader("Set-Cookie", "pasterino-redirect=". codeGen() ."; SameSite=None; Secure");
    $response->redirect("https://www.twitch.tv/btmc?origin=pasterino");
  }]);
  
  
  
  $router->get("/access", [function (Request $request, Response $response) {
    $state = $request->getHeader("X-State");
    
    if ($state === "") {
      $state = $request->query->looselyGet("s", "");
    }
    
    $db = getDatabase();
    
    if (!isset($db->loggedIn)) {
      $db->loggedIn = [];
    }
    
    $isLoggedIn = false;
    $token = "";
    
    foreach ($db->loggedIn as $user) {
      if ($user->state === $state) {
        $isLoggedIn = true;
        $token = $user->token;
        break;
      }
    }
    
    if (!$isLoggedIn) {
      $response->setStatusCode(Response::UNAUTHORIZED);
      $response->flush();
      return;
    }
    
    $response->setStatusCode(Response::OK);
    $response->send("You made it! [$token]");
  }]);
  
  
  
  $router->serve();