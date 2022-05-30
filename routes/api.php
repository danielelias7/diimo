<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([

    'middleware' => 'api',
    'prefix' => 'v1'

], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh',[AuthController::class,'refresh']);
    Route::get('me', [AuthController::class,'me']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('forgot-password', [AuthController::class,'forgotPassword']);
    Route::post('reset-password', [AuthController::class,'resetPassword'])->name('password.reset');

    //User
    Route::get('users', [UserController::class,'index']);
    Route::put('users/{id}', [UserController::class,'update']);
    Route::delete('users/{id}', [UserController::class,'destroy']);


    //Product
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::get('products/search', [ProductController::class, 'search']);
});