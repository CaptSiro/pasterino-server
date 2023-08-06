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
    public string $username, $profile_picture;
    
    
    
    static function by_id(Param $id): Result {
      try {
        $user = Database::get()->fetch(
          "SELECT id, username, profile_picture
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
          "SELECT users.id, username, profile_picture
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
    
    
    
    static function create(Param $id, Param $username, Param $pfp): Result {
      try {
        return success(Database::get()->statement(
          "INSERT INTO users (id, username, profile_picture)
          VALUE ($id, $username, $pfp)"
        ));
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
  }