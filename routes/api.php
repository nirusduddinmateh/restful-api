<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\PostCommentController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('register', AuthController::class . '@register');
    Route::post('login', AuthController::class . '@login');
    Route::post('logout', AuthController::class . '@logout');
    Route::post('refresh', AuthController::class . '@refresh');
    Route::get('me', AuthController::class . '@me');
});

Route::group([
    'middleware' => 'auth'
], function () {
    Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
    Route::resource('posts', PostController::class, ['except' => ['create', 'edit']]);
    Route::resource('posts.comments', PostCommentController::class, ['except' => ['create', 'edit']]);
});

