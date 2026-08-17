<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Config/routes.php';

try {
  $routes->validate();
} catch (Exception $e) {
  echo 'Error: ' . $e->getMessage();
  exit(1);
}

$app = AppFactory::create();
$routes->deploy($app);

$displayErrorDetails = getenv('APP_DEBUG') === '1';
$app->addErrorMiddleware($displayErrorDetails, true, true);

$app->run();