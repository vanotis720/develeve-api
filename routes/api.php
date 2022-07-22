<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;

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

Route::post('/signup', [AuthController::class, 'register']);
Route::post('/signin', [AuthController::class, 'login']);

Route::apiResource('/posts', PostController::class);
Route::apiResource('/categories', CategoryController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'profile']);
    Route::apiResource('/comments', CommentController::class);

});
