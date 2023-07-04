<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\TransactionController;

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

Route::group(['prefix' => 'product'], function() {
    Route::post('create', [ProductController::class, 'create']);
    Route::post('detail', [ProductController::class, 'detail']);
    Route::get('list', [ProductController::class, 'list']);
});

Route::group(['prefix' => 'cart'], function() {
    Route::post('add', [ProductController::class, 'add']);
    Route::post('checkout', [ProductController::class, 'checkout']);
    Route::get('list', [ProductController::class, 'list']);
});

Route::group(['prefix' => 'transaction'], function() {
    Route::post('list', [ProductController::class, 'list']);
    Route::post('order', [ProductController::class, 'order']);
});
