<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

return new Routes([
    Route::get('/users/{id}', 'ShowA'),
    Route::get('/users/{id}', 'ShowB'),
]);
