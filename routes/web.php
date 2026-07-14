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
        Route::get('/commodities/{commodity}/prices', [\App\Http\Controllers\IntelligenceController::class, 'commodityPrices'])->name('commodities.prices');
        Route::get('/ports', [\App\Http\Controllers\IntelligenceController::class, 'ports'])->name('ports');
        Route::post('/deep-analysis', [\App\Http\Controllers\IntelligenceController::class, 'deepAnalysis'])->name('deep-analysis');
    });

    // AI Decision Routes
    Route::prefix('ai-decision')->name('ai.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AiDecisionController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/simulate', [\App\Http\Controllers\AiDecisionController::class, 'simulate'])->name('simulate');
        Route::get('/history', [\App\Http\Controllers\AiDecisionController::class, 'history'])->name('history');
    });

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');
    });

    // Core Shipment Routes
    Route::resource('shipments', ShipmentController::class);

    // Administration Module
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/master-data', [\App\Http\Controllers\AdminController::class, 'masterData'])->name('master-data');
        Route::get('/api-management', [\App\Http\Controllers\AdminController::class, 'apiManagement'])->name('api-management');
        Route::get('/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
        Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('logs');
    });
});

require __DIR__.'/auth.php';
