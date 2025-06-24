<?php

use App\Filament\Resources\ProductResource;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalItemController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductResource::class);
Route::resource('rentals', RentalController::class);
Route::resource('rental-items', RentalItemController::class);
Route::resource('payments', PaymentController::class);
