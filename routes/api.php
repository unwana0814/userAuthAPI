<?php

use App\Http\Controllers\API\UserController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('user')->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::post('create', 'create');
        Route::post('login', 'login');
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::controller(UserController::class)->group(function () {
            Route::get('/{id}', 'getuser');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});

Route::get('users', [UserController::class, 'getusers'])->middleware('auth:sanctum');
