<?php
namespace App\Models\DB;
use App\Config\Database;
use Illuminate\Database\Capsule\Manager as Capsule;


class BaseModel {
  protected $table;
  protected $primaryKey = 'id';
  protected $fillable = [];
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

  private function hydrate(\stdClass $row): static {
    foreach (get_object_vars($row) as $key => $value) {
      $this->$key = $value;
    }

    return $this;
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
}