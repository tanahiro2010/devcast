<?php
namespace App\Config;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
  private static $instance = null;
  public static function init()
  {
    if (self::$instance === null) {
      self::$instance = new Capsule();
    }

    $capsule = self::$instance;
    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => getenv('DB_HOST') ?: '127.0.0.1',
      'port' => getenv('DB_PORT') ?: 3306,
      'database' => getenv('DB_NAME') ?: 'devcast',
      'username' => getenv('DB_USER') ?: 'admin',
      'password' => getenv('DB_PASSWORD') ?: 'admin',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci'
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
  }

  public static function getInstance(): Capsule
  {
    if (self::$instance === null) {
      self::init();
    }
    return self::$instance;
  }
}
