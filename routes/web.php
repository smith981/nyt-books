<?php

use App\Livewire\BestSellersSearch;
use Illuminate\Support\Facades\Route;

Route::prefix('web')->group(function () {
    Route::get('/best-seller-search',BestSellersSearch::class)->name('best-seller-search');
});
