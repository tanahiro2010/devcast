<?php
namespace App\Database;
use App\Config\Config;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
  private static $instance = null;

  public function __construct()
  {
    self::init();
  }

  public static function init()
  {
    if (self::$instance === null) {
      self::$instance = new Capsule();
    }

    $capsule = self::$instance;
    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => Config::env('DB_HOST') ?: '127.0.0.1',
      'port' => Config::env('DB_PORT') ?: 3306,
      'database' => Config::env('DB_NAME') ?: 'devcast',
      'username' => Config::env('DB_USER') ?: 'admin',
      'password' => Config::env('DB_PASSWORD') ?: 'admin',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci'
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
  }

  public function getInstance(): Capsule
  {
    if (self::$instance === null) {
      self::init();
    }
    return self::$instance;
  }
}
