<?php

use App\Http\Controllers\V1\NYT\BestSellerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return 'Welcome to the NYT Best Seller\'s API Wrapper!'; });

Route::prefix('1')->group(function () {
    Route::prefix('nyt')->group(function () {
        Route::get('best-sellers', [BestSellerController::class, 'index'])->name('api.v1.best-sellers.index');
    });
});
