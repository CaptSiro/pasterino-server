<?php
  
  namespace models;
  
  require_once __DIR__ . "/../lib/oakbase/oakbase.php";
  require_once __DIR__ . "/../lib/retval/retval.php";
  
  use Exc;
  use NotFoundExc;
  use OakBase\Database;
  use OakBase\MixedIndexingException;
  use OakBase\Param;
  use Result;
  
  
  
  class User {
    public int $id;
    public string $username, $profile_picture, $access_token, $state;
    
    
    
    static function by_id(Param $id): Result {
      try {
        $user = Database::get()->fetch(
          "SELECT id, username, profile_picture, access_token, state
          FROM users
          WHERE id = $id",
          self::class
        );
        if ($user === false || $user === null) {
          return fail(new NotFoundExc("User was not found with id: " . $id->value()));
        }
        
        return success($user);
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
    
    
    
    static function by_session_id(Param $id): Result {
      try {
        $user = Database::get()->fetch(
          "SELECT users.id, username, profile_picture, access_token, state
          FROM users
              JOIN pasterino.sessions s on users.id = s.users_id
                  AND s.id = $id
                  AND s.expires < CURRENT_TIMESTAMP",
          self::class
        );
        
        if ($user === false || $user === null) {
          return fail(new NotFoundExc("User was not found with id: " . $id->value()));
        }
        
        return success($user);
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
    
    
    
    static function by_token(Param $token): Result {
      try {
        $user = Database::get()->fetch(
          "SELECT users.id, username, profile_picture, access_token, state
          FROM users
              JOIN pasterino.sessions s on users.id = s.users_id
                  AND users.access_token = $token
                  AND s.expires < CURRENT_TIMESTAMP",
          self::class
        );
        
        if ($user === false || $user === null) {
          return fail(new NotFoundExc("User was not found with id: " . $token->value()));
        }
        
        return success($user);
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
    
    
    
    static function create(Param $username, Param $pfp, Param $access_token, Param $state): Result {
      try {
        return success(Database::get()->statement(
          "INSERT INTO users (username, profile_picture, access_token, state)
          VALUE ($username, $pfp, $access_token, $state)"
        ));
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
  }