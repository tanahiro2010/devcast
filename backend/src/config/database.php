<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();
$capsule->addConnection([
  'driver' => 'mysql',
  'host' => getenv('DB_HOST') ?: 'localhost',
  'port' => getenv('DB_PORT') ?: 3306,
  'database' => getenv('DB_NAME') ?: 'devcast',
  'username' => getenv('DB_USER') ?: 'admin',
  'password' => getenv('DB_PASSWORD') ?: 'admin',
  'charset' => 'utf8mb4',
  'collation' => 'utf8mb4_unicode_ci'
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

