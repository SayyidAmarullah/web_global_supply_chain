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
    // Global Search
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
    
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
        Route::post('/countries/{code}/favorite', [\App\Http\Controllers\IntelligenceController::class, 'toggleFavorite'])->name('countries.favorite');
        Route::get('/commodities', [\App\Http\Controllers\IntelligenceController::class, 'commodities'])->name('commodities');
        Route::get('/commodities/{commodity}/prices', [\App\Http\Controllers\IntelligenceController::class, 'commodityPrices'])->name('commodities.prices');
        Route::get('/currencies', [\App\Http\Controllers\IntelligenceController::class, 'currencies'])->name('currencies');
        Route::get('/exchange-rate', [\App\Http\Controllers\IntelligenceController::class, 'exchangeRate'])->name('exchange-rate');
        Route::get('/weather', [\App\Http\Controllers\IntelligenceController::class, 'weather'])->name('weather');
        Route::get('/ports', [\App\Http\Controllers\IntelligenceController::class, 'ports'])->name('ports');
        Route::get('/risk-alerts', [\App\Http\Controllers\IntelligenceController::class, 'riskAlerts'])->name('risk-alerts');
        Route::get('/news', [\App\Http\Controllers\IntelligenceController::class, 'news'])->name('news');
        Route::get('/google-news', [\App\Http\Controllers\IntelligenceController::class, 'fetchGoogleNews'])->name('google-news');
        Route::post('/deep-analysis', [\App\Http\Controllers\IntelligenceController::class, 'deepAnalysis'])->name('deep-analysis');
        Route::get('/compare', [\App\Http\Controllers\IntelligenceController::class, 'compare'])->name('compare');
        Route::get('/commodity-compare', [\App\Http\Controllers\IntelligenceController::class, 'commodityCompare'])->name('commodity-compare');
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
    Route::post('/shipments/{shipment}/start', [ShipmentController::class, 'startVoyage'])->name('shipments.start');
    Route::resource('shipments', ShipmentController::class);

    // Administration Module
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::put('/users/{user}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
        
        Route::get('/master-data', [\App\Http\Controllers\AdminController::class, 'masterData'])->name('master-data');
        
        // Shipments Management
        Route::get('/shipments', [\App\Http\Controllers\AdminController::class, 'shipments'])->name('shipments.index');
        Route::delete('/shipments/{shipment}', [\App\Http\Controllers\AdminController::class, 'deleteShipment'])->name('shipments.delete');
        
        Route::get('/ports', [\App\Http\Controllers\AdminController::class, 'ports'])->name('ports.index');
        Route::post('/ports', [\App\Http\Controllers\AdminController::class, 'storePort'])->name('ports.store');
        Route::delete('/ports/{port}', [\App\Http\Controllers\AdminController::class, 'deletePort'])->name('ports.delete');
        
        Route::get('/articles', [\App\Http\Controllers\AdminController::class, 'articles'])->name('articles.index');
        Route::post('/articles', [\App\Http\Controllers\AdminController::class, 'storeArticle'])->name('articles.store');
        Route::delete('/articles/{article}', [\App\Http\Controllers\AdminController::class, 'deleteArticle'])->name('articles.delete');
        
        Route::get('/api-management', [\App\Http\Controllers\AdminController::class, 'apiManagement'])->name('api-management');
        Route::post('/api-management/toggle', [\App\Http\Controllers\AdminController::class, 'toggleApi'])->name('api-management.toggle');
        
        Route::get('/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
        
        Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('logs');
        Route::post('/logs/clear', [\App\Http\Controllers\AdminController::class, 'clearLogs'])->name('logs.clear');
    });
});

require __DIR__.'/auth.php';
