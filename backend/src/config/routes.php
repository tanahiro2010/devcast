<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;

define('__BASE_CONTROLLER_PATH__', __DIR__ . '/../futures');

require __BASE_CONTROLLER_PATH__ . '/errors/controller.php';
require __BASE_CONTROLLER_PATH__ . '/health/controller.php';
require __BASE_CONTROLLER_PATH__ . '/auth/controller.php';
require __BASE_CONTROLLER_PATH__ . '/auth/callback/controller.php';

$v1_routes = null;

$routes = new Routes([
   Route::get('/', [new errorsController(), 'notFound']),
   Route::get('/health', [new HealthController(), 'health']),
   Route::group('/auth', [
      Route::controller(new AuthController(), [
         Route::get('/', 'oauthUrl')
      ]),
      Route::get('/callback', [new GithubCallbackController(), 'callback'])
   ]),
]);
