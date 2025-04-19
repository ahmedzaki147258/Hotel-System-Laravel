<?php

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
    return Inertia::render('ClientDashboard');
})->middleware(['auth:sanctum'])->name('client.dashboard');




Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');


// View any clients or show specific client
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

// Manager-only routes for editing, updating, and deleting clients
Route::middleware('can:update,client')->group(function () {
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
