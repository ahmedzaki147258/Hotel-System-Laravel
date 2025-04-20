<?php

use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('client/dashboard', function () {
    return Inertia::render('ClientDashboard', [
        'countries' => CountryHelper::getVisibleCountries()
    ]);
})->middleware(['auth:sanctum', 'auth:client'])->name('client.dashboard');


// Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
// Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');


// View any clients or show specific client
// Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
// Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('client')
        ->as('client.')
        ->namespace('App\Http\Controllers')
        ->group(function () {
            Route::post('/logout', [ClientController::class, 'logout'])->name('logout');
            Route::patch('/password/change', [ClientController::class, 'changePassword'])->name('password.change');
            Route::post('/image/update', [ClientController::class, 'updateAvatarImage'])->name('image.update');
            Route::patch('/profile/update', [ClientController::class, 'updateProfile'])->name('profile.update');
            Route::delete('/profile/delete', [ClientController::class, 'deleteProfile'])->name('profile.delete');
        });
});

// Manager-only routes for editing, updating, and deleting clients

// Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
// Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
// Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
