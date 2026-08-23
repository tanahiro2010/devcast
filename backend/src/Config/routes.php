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
use App\Futures\Auth\Profile\ProfileController;


$routes = new Routes([
  Route::get('/health', [new HealthController(), 'health']),
  Route::group('/auth', [
    Route::get('/', [new AuthController(), 'oauthUrl']),

    Route::group('/token', [
      Route::get('/access_token', [new AccessTokenController(), 'accessToken']),

      Route::middleware(new AuthMiddleware(), [
        Route::get('/refresh_token', [new RefreshTokenController(), 'refresh']),
      ]),
    ]),

    Route::middleware(new AuthMiddleware(), [
      Route::get('/profile', [new ProfileController(), 'getProfile']),
    ]),

    Route::get('/callback', [new CallbackController(), 'callback']),
  ]),



  // Catch-all: must stay last so it doesn't shadow the routes above.
  // FastRoute wildcard syntax is `{name:.*}`, not `*`.
  Route::get('/{path:.*}', [new ErrorsController(), 'notFound']),
]);
