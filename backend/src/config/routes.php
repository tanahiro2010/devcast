<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;

require __DIR__ . '/../futures/health/controller.php';

$v1_routes = null;

$routes = new Routes([
   Route::get('/', function (Request $_, Response $response): Response {
      $response->getBody()->write(json_encode([
         'name' => 'DevCast API',
         'status' => 'ok',
      ]));
      return $response->withHeader('Content-Type', 'application/json');
   }),
   Route::get('/health', [new HealthController(), 'health'])
]);