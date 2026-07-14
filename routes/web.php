<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Shipment Redirection Routes
    Route::get('/shipments/{shipment}/redirect', [ShipmentController::class, 'redirect'])->name('shipments.redirect');
    Route::post('/shipments/{shipment}/redirect', [ShipmentController::class, 'storeRedirect'])->name('shipments.storeRedirect');
    
    // Interactive World Map Routes
    Route::get('/map', [\App\Http\Controllers\MapController::class, 'index'])->name('map.index');
    Route::get('/api/map/data', [\App\Http\Controllers\MapController::class, 'getMapData'])->name('map.data');

    // Global Intelligence Routes
    Route::prefix('intelligence')->name('intelligence.')->group(function () {
        Route::get('/', [\App\Http\Controllers\IntelligenceController::class, 'index'])->name('index');
        Route::get('/countries', [\App\Http\Controllers\IntelligenceController::class, 'countries'])->name('countries');
        Route::get('/commodities', [\App\Http\Controllers\IntelligenceController::class, 'commodities'])->name('commodities');
        Route::get('/ports', [\App\Http\Controllers\IntelligenceController::class, 'ports'])->name('ports');
    });

    // AI Decision Routes
    Route::prefix('ai-decision')->name('ai.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AiDecisionController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/simulate', [\App\Http\Controllers\AiDecisionController::class, 'simulate'])->name('simulate');
        Route::get('/history', [\App\Http\Controllers\AiDecisionController::class, 'history'])->name('history');
    });

    // Core Shipment Routes
    Route::resource('shipments', ShipmentController::class);
});

require __DIR__.'/auth.php';
