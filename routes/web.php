<?php

use App\Http\Controllers\AppealController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CitationController;
use App\Http\Controllers\ClampingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\OwnerPortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('citations', CitationController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('clamping', ClampingController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('appeals', AppealController::class);
    Route::get('releases', [ReleaseController::class, 'index'])->name('releases.index');
    Route::get('releases/create/{clamping}', [ReleaseController::class, 'create'])->name('releases.create');
    Route::post('releases/{clamping}', [ReleaseController::class, 'store'])->name('releases.store');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);

    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('citations', [OwnerPortalController::class, 'citations'])->name('citations');
        Route::get('vehicles', [OwnerPortalController::class, 'vehicles'])->name('vehicles');
        Route::get('clamping', [OwnerPortalController::class, 'clamping'])->name('clamping');
    });
});
