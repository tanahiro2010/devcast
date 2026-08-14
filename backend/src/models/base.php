<?php
namespace App\Models\DB;
use App\Config\Database;
use Illuminate\Database\Capsule\Manager as Capsule;


#[\AllowDynamicProperties]
class BaseModel {
  protected $table;
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $properties = [];
  protected $database;

  public function __construct() {
    $database = new Database();
    $this->database = $database->getInstance();
  }

  private static function getDatabaseInstance(): Capsule {
    $database = new Database();
    return $database->getInstance();
  }

  static function create($attributes = []): static {
    // Implementation for creating a new record in the database
    $instance = self::getDatabaseInstance();
    $model = new static();

    $result = $instance->table($model->table)->insert($attributes);
    if (!$result) {
      throw new \Exception("Failed to create record in " . static::class);
    }

    $row = static::where($attributes)->first();
    if ($row === null) {
      throw new \Exception("Failed to load created record in " . static::class);
    }

    return $model->hydrate($row);
  }

  static function where($attributes = []) {
    // Implementation for querying records based on a condition
    $model = new static();
    $database = self::getDatabaseInstance();
    $query = $database->table($model->table);

    foreach ($attributes as $key => $value) {
      if (in_array($key, $model->fillable, true) || $key === $model->primaryKey) {
        $query = $query->where($key, $value);
      } else {
        throw new \Exception("Property $key does not exist on " . static::class);
      }
    }

    return $query;
  }

  public static function all() {
    $model = new static();
    $database = self::getDatabaseInstance();
    return $database->table($model->table)->get();
  }

  public static function find(string | int $id) {
    $model = new static();
    $database = self::getDatabaseInstance();
    $row = $database->table($model->table)->where($model->primaryKey, $id)->first();

    if ($row === null) {
      return null;
    }

    return $model->hydrate($row);
  }

  public function destroy(): true {
    $database = $this->database;
    try {
      $database->table($this->table)->where($this->primaryKey, $this->properties[$this->primaryKey])->delete();
      return true;
    } catch (\Exception $e) {
      throw new \Exception("Failed to delete record in " . static::class . ": " . $e->getMessage());      
    }
  }

  public function __get($name) {
    if (array_key_exists($name, $this->properties)) {
      return $this->properties[$name];
    }
    throw new \Exception("Property $name does not exist on " . static::class);
  }

  public function __set($name, $value) {
    if (in_array($name, $this->fillable, true) || $name === $this->primaryKey) {
      $this->properties[$name] = $value;
    } else {
      throw new \Exception("Property $name does not exist on " . static::class);
    }
  }

  public function toArray(): array {
    return $this->properties;
  }

  public function save(): true {
    $database = $this->database;
    try {
      if (isset($this->properties[$this->primaryKey])) {
        // Update existing record
        $database->table($this->table)
          ->where($this->primaryKey, $this->properties[$this->primaryKey])
          ->update($this->properties);
      } else {
        // Insert new record
        $id = $database->table($this->table)->insertGetId($this->properties);
        $this->properties[$this->primaryKey] = $id;
      }
      return true;
    } catch (\Exception $e) {
      throw new \Exception("Failed to save record in " . static::class . ": " . $e->getMessage());
    }
  }



  private function hydrate(\stdClass $row): static {
    foreach (get_object_vars($row) as $key => $value) {
      if (in_array($key, $this->fillable, true) || $key === $this->primaryKey) {
        $this->properties[$key] = $value;
      }
    }

    return $this;
  }
}