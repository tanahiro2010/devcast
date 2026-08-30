<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the CLI.');
}

$name = $argv[1] ?? null;

if (!$name) {
    fwrite(STDERR, "Usage: php scripts/make_migration.php <migration_name>\n");
    exit(1);
}

$name = preg_replace('/[^a-zA-Z0-9_]+/', '_', $name);

$migrationsDir = __DIR__ . '/../src/Database/migrations';

$existing = glob($migrationsDir . '/*.php');
$lastNumber = 0;

foreach ($existing as $file) {
    if (preg_match('/^(\d+)_/', basename($file), $m)) {
        $lastNumber = max($lastNumber, (int) $m[1]);
    }
}

$number = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
$fileName = "{$number}_{$name}.php";
$path = "{$migrationsDir}/{$fileName}";

if (file_exists($path)) {
    fwrite(STDERR, "Migration already exists: {$fileName}\n");
    exit(1);
}

$template = <<<PHP
<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule \$capsule) {
        //
    },
    'down' => function (Capsule \$capsule) {
        //
    },
];

PHP;

file_put_contents($path, $template);

echo "Created: src/Database/migrations/{$fileName}\n";
