<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CartsController;

#Login
Route::prefix('user/')->group(static function () {
    Route::post("register", [UserController::class, 'register']);
    Route::post("login", [UserController::class, 'login']);
});

Route::group(['middleware' => ["auth:sanctum"] ], function() {
    Route::get("user-profile", [UserController::class, 'userProfile']);
    Route::get("logout", [UserController::class, 'logout']);
    #products
    Route::prefix('products/')->group(static function () {
        Route::get("", [ProductsController::class, 'getProducts']);
        Route::get("{identifier}", [ProductsController::class, 'getProduct']);
        Route::post("", [ProductsController::class, 'addProduct']);
        Route::put("{identifier}", [ProductsController::class, 'updateProduct']);
        Route::delete('{identifier}', [ProductsController::class, 'deleteProduct']);
    });
    #cart
    Route::prefix('cart/')->group(static function () {
        Route::prefix('products/')->group(static function () {
                Route::get('', [CartsController::class, 'getProducts']);
                Route::post('', [CartsController::class, 'addProduct']);
                Route::delete('/{externalId}', [CartsController::class, 'deleteProduct']);
            });
        Route::post('checkout', [CartsController::class, 'checkout']);
    });
    
});