<?php

// routes/web.php

use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

return [
    Route::prefix('/api', function () {
        Route::get('/create', [TestController::class, 'create']);
        Route::post('/post', [TestController::class, 'store']);

        Route::prefix('/users', function () {
            Route::get('/', [TestController::class, 'index']);
        });
    })
];