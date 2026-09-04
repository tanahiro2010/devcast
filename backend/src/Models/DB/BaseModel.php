<?php
namespace App\Models\DB;
use App\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;


#[\AllowDynamicProperties]
class BaseModel implements \JsonSerializable
{
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = [];
    /** @var array<string, mixed> */
    protected array $properties = [];
    protected Capsule $database;

    public function __construct()
    {
        $database = new Database();
        $this->database = $database->getInstance();
    }

    private static function getDatabaseInstance(): Capsule
    {
        $database = new Database();
        return $database->getInstance();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    static function create(array $attributes = []): static
    {
        // Implementation for creating a new record in the database
        $instance = self::getDatabaseInstance();
        $model = new static();

        $allowedKeys = array_merge($model->fillable, [$model->primaryKey]);
        $filtered = array_intersect_key($attributes, array_flip($allowedKeys));

        $result = $instance->table($model->table)->insert($filtered);
        if (!$result) {
            throw new \Exception("Failed to create record in " . static::class);
        }

        $created = static::firstWhere($filtered);
        if ($created === null) {
            throw new \Exception("Failed to load created record in " . static::class);
        }

        return $created;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    static function where(array $attributes = []): Builder
    {
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

    /**
     * @param array<string, mixed> $attributes
     */
    static function firstWhere(array $attributes = []): ?static
    {
        $row = static::where($attributes)->first();
        if ($row === null) {
            return null;
        }

        $model = new static();
        return $model->hydrate($row);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return static[]
     */
    public static function whereAll(array $attributes = []): array
    {
        $rows = static::where($attributes)->get();
        return $rows->map(function ($row) {
            $model = new static();
            return $model->hydrate($row);
        })->all();
    }

    /**
     * @param string[] $columns
     * @return Collection<int, static>
     */
    public static function all(array $columns = ['*']): Collection
    {
        $model = new static();
        $database = self::getDatabaseInstance();
        return $database->table($model->table)->get($columns)->map(function ($row) use ($model) {
            return $model->hydrate($row);
        });
    }

    public static function find(string | int $id): ?static
    {
        $model = new static();
        $database = self::getDatabaseInstance();
        $row = $database->table($model->table)->where($model->primaryKey, $id)->first();

        if ($row === null) {
            return null;
        }

        return $model->hydrate($row);
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    public function update(array $attributes): static
    {
        $this->fill($attributes)->save();
        return $this;
    }

    public function destroy(): true
    {
        $database = $this->database;
        try {
            $database->table($this->table)->where($this->primaryKey, $this->properties[$this->primaryKey])->delete();
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete record in " . static::class . ": " . $e->getMessage());      
        }
    }

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }
        throw new \Exception("Property $name does not exist on " . static::class);
    }

    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, $this->fillable, true) || $name === $this->primaryKey) {
            $this->properties[$name] = $value;
        } else {
            throw new \Exception("Property $name does not exist on " . static::class);
        }
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function save(): true
    {
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

    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }
        throw new \Exception("Property $key does not exist on " . static::class);
    }

    /**
     * @param class-string<BaseModel> $related
     * $foreignKey is a column on this model that points at $ownerKey (defaults to $related's primary key).
     */
    protected function belongsTo(string $related, string $foreignKey, ?string $ownerKey = null): ?BaseModel
    {
        $value = $this->properties[$foreignKey] ?? null;
        if ($value === null) {
            return null;
        }

        if ($ownerKey === null) {
            return $related::find($value);
        }

        return $related::firstWhere([$ownerKey => $value]);
    }

    /**
     * @param class-string<BaseModel> $related
     * $foreignKey is a column on $related that points back at this model's $localKey (defaults to this model's primary key).
     * @return BaseModel[]
     */
    protected function hasMany(string $related, string $foreignKey, ?string $localKey = null): array
    {
        $localKey ??= $this->primaryKey;
        $value = $this->properties[$localKey] ?? null;
        if ($value === null) {
            return [];
        }

        return $related::whereAll([$foreignKey => $value]);
    }

    /**
     * @param class-string<BaseModel> $related
     * $foreignKey is a column on $related that points back at this model's $localKey (defaults to this model's primary key).
     */
    protected function hasOne(string $related, string $foreignKey, ?string $localKey = null): ?BaseModel
    {
        $localKey ??= $this->primaryKey;
        $value = $this->properties[$localKey] ?? null;
        if ($value === null) {
            return null;
        }

        return $related::firstWhere([$foreignKey => $value]);
    }

    private function hydrate(\stdClass $row): static
    {
        foreach (get_object_vars($row) as $key => $value) {
            if (in_array($key, $this->fillable, true) || $key === $this->primaryKey) {
                $this->properties[$key] = $value;
            }
        }

        return $this;
    }
}