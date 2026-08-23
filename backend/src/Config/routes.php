<?php

use Tanahiro2010\SlimRouterDsl\Routes;
use Tanahiro2010\SlimRouterDsl\Route;
use App\Middleware\AuthMiddleware;
use App\Futures\Errors\ErrorsController;
use App\Futures\Health\HealthController;
use App\Futures\Auth\AuthController;
use App\Futures\Auth\AccessToken\AccessTokenController;
use App\Futures\Auth\RefreshToken\RefreshTokenController;
use App\Futures\Auth\Callback\CallbackController;


$routes = new Routes([
  Route::get('/', [new ErrorsController(), 'notFound']),
  Route::get('/health', [new HealthController(), 'health']),
  Route::group('/auth', [
    Route::get('/', [new AuthController(), 'oauthUrl']),

    Route::group('/token', [
      Route::get('/access_token', [new AccessTokenController(), 'accessToken']),

      Route::middleware(new AuthMiddleware(), [
        Route::get('/refresh_token', [new RefreshTokenController(), 'refresh']),
      ]),
    ]),

    Route::get('/callback', [new CallbackController(), 'callback']),
  ]),
]);
