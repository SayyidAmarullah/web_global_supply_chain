<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Shipment Redirection Routes
    Route::get('/shipments/{shipment}/redirect', [ShipmentController::class, 'redirect'])->name('shipments.redirect');
    Route::post('/shipments/{shipment}/redirect', [ShipmentController::class, 'storeRedirect'])->name('shipments.storeRedirect');
    
    // Core Shipment Routes
    Route::resource('shipments', ShipmentController::class);
});

require __DIR__.'/auth.php';
