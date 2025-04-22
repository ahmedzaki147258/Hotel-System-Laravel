<?php

use App\Helpers\CountryHelper;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientManagementController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ManagerController;

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


Route::middleware(['auth'])->group(function () {
    // Client management routes
    Route::resource('clients', ClientManagementController::class);
    Route::get('/my-approved-clients', [ClientManagementController::class, 'myApprovedClients'])->name('clients.approved');
    Route::post('/clients/{client}/approve', [ClientManagementController::class, 'approve'])->name('clients.approve');

    // Receptionists management routes
    Route::resource('receptionists', ReceptionistController::class);
    Route::post('/receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('receptionists.ban');
    Route::post('/receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('receptionists.unban');

    // Managers management routes
    Route::resource('managers', ManagerController::class);
    Route::post('/managers/{manager}/ban', [ManagerController::class, 'ban'])->name('managers.ban');
    Route::post('/managers/{manager}/unban', [ManagerController::class, 'unban'])->name('managers.unban');
});


// Route::match(['PUT', 'POST'], 'receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');

// // View any clients or show specific client
// Route::get('/receptionists', [ReceptionistController::class, 'index'])->name('receptionists.index');
// // Display the receptionists edit page
// Route::get('/receptionists/{receptionist}/edit', [ReceptionistController::class, 'edit'])->name('receptionists.edit');
// Route::get('/receptionists/create', [ReceptionistController::class, 'create'])->name('receptionists.create');

// Route::delete('receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionists.destroy');
// // Update client information
// Route::put('receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');


//Route::post('/receptionists', [ReceptionistController::class, 'store'])->name('receptionists.store');


// Route resource for floors
Route::middleware(['auth'])->group(function () {
    Route::resource('floors', FloorController::class);
});

// Route resource for rooms
Route::middleware(['auth'])->group(function () {
    Route::resource('rooms', RoomController::class);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
