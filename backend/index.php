<?php
use Slim\Factory\AppFactory;
use App\Middleware\CorsMiddleware;
use App\Config\Routes;

require_once __DIR__ . '/vendor/autoload.php';

$routes = Routes::build();

try {
    $routes->validate();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit(1);
}

$app = AppFactory::create();
$routes->deploy($app);
$app->addMiddleware(new CorsMiddleware());
$app->addBodyParsingMiddleware();

$displayErrorDetails = getenv('APP_DEBUG') === '1';
$app->addErrorMiddleware($displayErrorDetails, true, true);

$app->run();