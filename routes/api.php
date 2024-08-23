<?php

use App\Http\Controllers\API\CarnetController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1/carnet')->name('api.v1.carnet.')->group(function () {
    Route::post('/', [CarnetController::class, 'store'])->name('store');
    Route::get('/{carnet}', [CarnetController::class, 'show'])->name('show');
});
