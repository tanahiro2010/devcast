<?php
use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;
use App\Middleware\CorsMiddleware;
use App\Futures\Errors\ErrorsController;
use App\Futures\Health\HealthController;
use App\Futures\Auth\AuthController;
use App\Futures\Auth\Callback\CallbackController;


$routes = new Routes([
  Route::middleware(new CorsMiddleware(), [
    Route::get('/', [new ErrorsController(), 'notFound']),
    Route::get('/health', [new HealthController(), 'health']),
    Route::group('/auth', [
      Route::controller(new AuthController(), [
        Route::get('/', 'oauthUrl')
      ]),
      Route::get('/callback', [new CallbackController(), 'callback'])
   ]),
  ]),
]);
