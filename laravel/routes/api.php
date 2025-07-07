<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BulkActionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], function () {

    Route::middleware(['auth:api'])->group(function () {

        // Auth Routes
        Route::group([
            'prefix' => 'auth',
            'controller' => AuthController::class,
        ], function () {
            Route::post('logout', 'logout');
            Route::post('refresh-token', 'refreshToken');
            Route::get('me', 'me');
        });

        Route::group(
            [
                'prefix' => 'users',
                'controller' => UserController::class
            ],
            function () {
                Route::get('/', 'index');
            }
        );

        Route::group(
            [
                'prefix' => 'media',
                'controller' => MediaController::class
            ],
            function () {
                Route::get('/', 'index');
                Route::post('upload', 'upload');
                Route::delete('destroy', 'destroy');
            }
        );

        Route::prefix('bulk')
            ->controller(BulkActionController::class)
            ->group(function () {
                Route::post('{model}/delete', 'bulkDelete');
                Route::post('{model}/archive', 'bulkArchive');
            });
    });


    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
});


// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'auth'
// ], function ($router) {
//     Route::post('/register', [AuthController::class, 'register'])->name('register');
//     Route::post('/login', [AuthController::class, 'login'])->name('login');
//     Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
//     Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
//     Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
// });
