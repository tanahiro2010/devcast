<?php
use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;

require __DIR__ . '../futures/health/health.controller.php';

$v1_routes = null;

$routes = new Routes([
  Route::group('/api', [
    Route::get('/health', [new HealthController(), 'health'])
  ])
]);