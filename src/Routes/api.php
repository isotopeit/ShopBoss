<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Isotope\ShopBoss\Http\Controllers\XlImportController;
use Isotope\ShopBoss\Http\Controllers\CustomersController;
use Isotope\ShopBoss\Http\Controllers\Api\ProductApiController;
use Isotope\ShopBoss\Http\Controllers\Api\PurchaseApiController;
use Isotope\ShopBoss\Http\Controllers\PurchasesReturnController;

Route::prefix('api')->middleware(['web', 'auth'])->group(function () {

    Route::prefix('select2')->group(function () {
        Route::get('/products', [ProductApiController::class, 'productSelect2']);
        Route::get('/purchases', [PurchaseApiController::class, 'purchaseSelect2']);
        Route::get('/sales', [PurchaseApiController::class, 'saleSelect2']);
    });
    
    Route::get('/purchases/{id}', [PurchasesReturnController::class, 'purchaseData']);

    Route::get('/products/{id}', [ProductApiController::class, 'product']);
    Route::get('/purchases/{id}', [PurchaseApiController::class, 'purchase']);
    Route::get('/sale/{id}', [PurchaseApiController::class, 'sale']);

    Route::post('/customer-store', [CustomersController::class, 'customerStore']);
    Route::post('/customer-store', [CustomersController::class, 'customerStore']);
    Route::post('/xl-product-create', [XlImportController::class, 'xlProductCreate']);
    
});