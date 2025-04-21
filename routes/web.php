<?php

use App\Helpers\CountryHelper;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientManagementController;
use App\Http\Controllers\ReceptionistController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('client')
        ->as('client.')
        ->group(function () {
            Route::post('/logout', [ClientController::class, 'logout'])->name('logout');
            Route::patch('/password/change', [ClientController::class, 'changePassword'])->name('password.change');
            Route::post('/image/update', [ClientController::class, 'updateAvatarImage'])->name('image.update');
            Route::patch('/profile/update', [ClientController::class, 'updateProfile'])->name('profile.update');
            Route::delete('/profile/delete', [ClientController::class, 'deleteProfile'])->name('profile.delete');
            Route::get('/reservations', [ClientController::class, 'getReservations'])->name('reservations.index');
            Route::get('/floors', [ClientController::class, 'getFloors'])->name('floors.index');
            Route::get('/floors/{floorId}/rooms', [ClientController::class, 'getRoomsByFloor'])->name('rooms.index');
            Route::post('/reservations', [ClientController::class, 'makeReservation'])->name('reservations.store');
        });
});


Route::get('/clients/create', [ClientManagementController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientManagementController::class, 'store'])->name('clients.store');


// View any clients or show specific client
Route::get('/clients', [ClientManagementController::class, 'index'])->name('clients.index');
Route::get('/clients/{client}', [ClientManagementController::class, 'show'])->name('clients.show');

// Manager-only routes for editing, updating, and deleting clients

Route::get('/clients/{client}/edit', [ClientManagementController::class, 'edit'])->name('clients.edit');

Route::put('/clients/{client}', [ClientManagementController::class, 'update'])->name('clients.update');
Route::delete('/clients/{client}', [ClientManagementController::class, 'destroy'])->name('clients.destroy');

//receptionists
Route::match(['PUT', 'POST'], 'receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');

// View any clients or show specific client
Route::get('/receptionists', [ReceptionistController::class, 'index'])->name('receptionists.index');
// Display the receptionists edit page
Route::get('/receptionists/{receptionist}/edit', [ReceptionistController::class, 'edit'])->name('receptionists.edit');
Route::get('/receptionists/create', [ReceptionistController::class, 'create'])->name('receptionists.create');

Route::delete('receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionists.destroy');
// Update client information
Route::put('receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');


Route::post('/receptionists', [ReceptionistController::class, 'store'])->name('receptionists.store');
Route::post('/receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('receptionists.ban');
Route::post('/receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('receptionists.unban');



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
