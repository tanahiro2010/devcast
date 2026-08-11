<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/database.php';

/** @var \Illuminate\Database\Capsule\Manager $capsule */

$schema = $capsule->schema();

if (!$schema->hasTable('migrations')) {
    $schema->create('migrations', function ($table) {
        $table->string('migration')->primary();
        $table->timestamp('ran_at')->useCurrent();
    });
}

$ran = $capsule->table('migrations')->pluck('migration')->all();

$files = glob(__DIR__ . '/../src/database/migrations/*.php');
sort($files);

foreach ($files as $file) {
    $name = basename($file, '.php');

    if (in_array($name, $ran, true)) {
        continue;
    }

    $migration = require $file;
    $migration['up']($capsule);

    $capsule->table('migrations')->insert(['migration' => $name, 'ran_at' => date('Y-m-d H:i:s')]);

    echo "Migrated: {$name}\n";
}

echo "Done.\n";
