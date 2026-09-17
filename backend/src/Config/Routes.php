<?php
namespace App\Config;

use Tanahiro2010\SlimRouterDsl\Routes as RouterRoutes;
use Tanahiro2010\SlimRouterDsl\Route;
use App\Middleware\AuthMiddleware;
use App\Features\Errors\ErrorsController;
use App\Features\Health\HealthController;
use App\Features\Auth\AuthController;
use App\Features\Auth\AccessToken\AccessTokenController;
use App\Features\Auth\RefreshToken\RefreshTokenController;
use App\Features\Auth\Callback\CallbackController;
use App\Features\Auth\Profile\ProfileController;
use App\Features\Version1\Version1Controller;
use App\Features\Version1\Providers\ProvidersController;
use App\Features\Version1\Articles\ArticlesController;

class Routes
{
    private static ?RouterRoutes $instance = null;

    public static function build(): RouterRoutes
    {
        if (self::$instance === null) {
            self::$instance = new RouterRoutes([
                Route::get('/health', [new HealthController(), 'health']),
                Route::group('/auth', [
                    Route::get('/', [new AuthController(), 'oauthUrl']),

                    Route::group('/token', [
                        Route::post('/access_token', [new AccessTokenController(), 'accessToken']),

                        Route::middleware(new AuthMiddleware(), [
                            Route::get('/refresh_token', [new RefreshTokenController(), 'refresh']),
                        ]),
                    ]),

                    Route::middleware(new AuthMiddleware(), [
                        Route::get('/profile', [new ProfileController(), 'getProfile']),
                    ]),

                    Route::get('/callback', [new CallbackController(), 'callback']),
                ]),

                Route::middleware(new AuthMiddleware(), [
                    Route::group('/v1', [
                        Route::get('/', [new Version1Controller(), 'version1']),
                        Route::group('/providers', [
                            Route::controller(new ProvidersController(), [
                                Route::get('/', 'getProviders'),
                                Route::post('/', 'registerProvider'),
                                Route::put('/', 'updateProvider'),
                                Route::delete('/', 'deleteProvider'),
                            ]),
                        ]),
                        Route::group('/articles', [
                            Route::controller(new ArticlesController(), [
                                Route::get('/', 'getArticles'),
                                Route::post('/', 'createArticle')
                            ])
                        ])
                    ])
                ]),

                // Catch-all: must stay last so it doesn't shadow the routes above.
                // FastRoute wildcard syntax is `{name:.*}`, not `*`.
                Route::get('/{path:.*}', [new ErrorsController(), 'notFound']),
            ]);
        }

        return self::$instance;
    }
}
