<?php
  
  namespace models;
  
  require_once __DIR__ . "/../lib/oakbase/oakbase.php";
  require_once __DIR__ . "/../lib/retval/retval.php";
  require_once __DIR__ . "/../constants.php";
  require_once __DIR__ . "/Session.php";
  
  use Exc;
  use Exception;
  use NotFoundExc;
  use OakBase\Database;
  use OakBase\MixedIndexingException;
  use OakBase\Param;
  use OakBase\SideEffect;
  use Result;
  use function OakBase\param;
  
  
  
  class Count {
    public int $count;
  }