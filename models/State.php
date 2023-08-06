<?php
  
  namespace models;
  
  import("oakbase");
  import("retval");
  
  import_model("Session");
  import_model("Count");
  
  use Exc;
  use Exception;
  use NotFoundExc;
  use OakBase\Database;
  use OakBase\MixedIndexingException;
  use OakBase\Param;
  use OakBase\SideEffect;
  use Result;
  use function OakBase\param;
  
  
  
  class State {
    public string $state, $redirect;
    
    
    
    static function create(Param $redirect): Result {
      $id = Session::generate_id();
      
      if ($id->isFailure()) {
        return $id;
      }
      
      $state = param($id->getSuccess());
      
      try {
        Database::get()->statement(
          "INSERT INTO states (state, redirect) VALUE ($state, $redirect)"
        );
      } catch (MixedIndexingException $e) {
        return fail(new Exc($e->getMessage()));
      }
      
      return $id;
    }
    
    
    
    static function by_state(Param $state): Result {
      try {
        $s = Database::get()->fetch(
          "SELECT state, redirect
          FROM states
          WHERE state = $state",
          self::class
        );
        
        if ($s === false || $s === null) {
          return fail(new NotFoundExc("Could not find state: ". $state->value()));
        }
        
        return success($s);
      } catch (MixedIndexingException $e) {
        return fail_e($e);
      }
    }
    
    
    
    static function delete(Param $state): Result {
      try {
        return success(Database::get()->statement(
          "DELETE FROM states
          WHERE state = $state
          LIMIT 1"
        ));
      } catch (MixedIndexingException $e) {
        return fail(new Exc($e->getMessage()));
      }
    }
    
    
    
    static function exists(Param $state): Result {
      try {
        $states = Database::get()->fetch(
          "SELECT COUNT(*) as count
          FROM states
          WHERE state = $state",
          Count::class
        );
        
        if ($states === false || $states === null) {
          return success(false);
        }
        
        return success($states->count === 1);
      } catch (MixedIndexingException $e) {
        return fail(new Exc($e->getMessage()));
      }
    }
  }