<?php
  
  namespace OakBase;
  
  use PDO;
  use PDOStatement;
  use stdClass;

  require_once __DIR__ . "/buffer/ParamBuffer.php";
  require_once __DIR__ . "/exceptions/CreationException.php";
  require_once __DIR__ . "/exceptions/MixedIndexingException.php";
  require_once __DIR__ . "/SideEffect.php";
  
  class Database {
    private PDO $connection;
    
    public function connection () {
      return $this->connection;
    }
    
    
    
    private static Config $config;
    
    public static function configure (Config $config) {
      self::$config = $config;
    }
  
  
  
    private static Database $instance;
    
    public static function get(): Database {
      if (!isset($instance)) {
        self::$instance = new Database();
      }
      
      return self::$instance;
    }
    
    
  
    /**
     * @throws CreationException
     */
    public function __construct() {
      if (!isset(self::$config)) {
        throw new CreationException("Must set config object before creating connection to database. Use Database::configure() with custom object that implements Config or use BasicConfig class.");
      }
      
      $connectionString = "mysql:host=". self::$config->host()
        .";port=". self::$config->port()
        .";dbname=". self::$config->database_name()
        .";charset=". self::$config->charset();
      $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // errors from MySQL will appear as PHP Exceptions
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false // SQL injection
      ];
      $this->connection = new PDO($connectionString, self::$config->user(), self::$config->password(), $opt);
    }
    
    
    
    public function last_inserted_ID() {
      return $this->connection->lastInsertId();
    }
    
    
    
    private static function count_params (string $sql): int {
      $no_string_literals = "";
  
      $in_string = false;
      for ($i = 0; $i < strlen($sql); $i++) {
        if (in_array($sql[$i], ["'", '"'])) {
          $in_string = !$in_string;
          continue;
        }
    
        if ($in_string === true) {
          continue;
        }
    
        $no_string_literals .= $sql[$i];
      }
  
      return intval(preg_match_all("/([:?])/", $no_string_literals));
    }
  
  
    
    /**
     * @param string|Query $query
     * @return Buffer
     */
    private static function get_buffer ($query): Buffer {
      if ($query instanceof Query) {
        return $query->params();
      }
      
      return ParamBuffer::get();
    }
  
  
  
    /**
     * @param string|Query $query
     * @return string
     */
    private static function get_string ($query): string {
      if ($query instanceof Query) {
        return $query->string();
      }
    
      return $query;
    }
  
  
    /**
     * @param PDOStatement $statement
     * @param string|Query $query
     * @throws MixedIndexingException
     */
    private static function bind_params (PDOStatement $statement, $query) {
      $index = 1;
      $indexationType = "--initial--";
      
      $iteration = 0;
      $count = self::count_params(self::get_string($query));
      $buf = self::get_buffer($query);
      
      while ($iteration < $count && !$buf->is_empty()) {
        $param = $buf->shift();
        $name = $param->name() ?? $index++;
    
        if ($indexationType !== gettype($name) && $indexationType !== "--initial--") {
          throw new MixedIndexingException("Cannot use named param logic as well as indexed param logic. Got index: ". $name);
        }
    
        $indexationType = gettype($name);
  
        $statement->bindValue($name, $param->value(), $param->type());
        $iteration++;
      }
    }
  
  
    
    /**
     * Run a query that does not return any rows such as UPDATE, DELETE, INSERT or TRUNCATE.
     *
     * @param string|Query $query
     * @return SideEffect
     * @throws MixedIndexingException
     */
    public function statement ($query): SideEffect {
      $stmt = $this->connection->prepare(self::get_string($query));
      self::bind_params($stmt, $query);
      
      $stmt->execute();

      return new SideEffect(
        $this->last_inserted_ID(),
        $stmt->rowCount()
      );
    }
  
  
    
    /**
     * Fetch a single row.
     *
     * @param string|Query $query
     * @param string $class
     * @return mixed
     * @throws MixedIndexingException
     */
    public function fetch ($query, string $class = stdClass::class) {
      $stmt = $this->connection->prepare(self::get_string($query));
      self::bind_params($stmt, $query);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
      
      return $stmt->fetch();
    }
  
  
    
    /**
     * Fetch multiple rows.
     *
     * @param string|Query $query
     * @param string $class
     * @return array|false
     * @throws MixedIndexingException
     */
    public function fetch_all ($query, string $class = stdClass::class) {
      $stmt = $this->connection->prepare(self::get_string($query));
      self::bind_params($stmt, $query);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
      
      return $stmt->fetchAll();
    }
  }