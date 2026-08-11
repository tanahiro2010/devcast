<?php
use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;

$v1_routes = null;

$routes = new Routes([
  Route::group('/api', [
    Route::get('/v1', function ($request, $response) {
      $response->getBody()->write('Hello, World!');
      return $response;
    }),
  ])
]);