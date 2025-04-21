<?php

use App\Helpers\CountryHelper;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StaffController;

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

// Client management routes with proper middleware
Route::middleware(['auth'])->group(function () {
    // View any clients or show specific client
    Route::get('/clients', [StaffController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [StaffController::class, 'show'])->name('clients.show');
    
    // My approved clients (for receptionists)
    Route::get('/my-approved-clients', [StaffController::class, 'myApprovedClients'])->name('clients.approved');
    
    // Approve client route
    Route::post('/clients/{client}/approve', [StaffController::class, 'approve'])->name('clients.approve');
    
    // Create and store client routes
    Route::get('/clients/create', [StaffController::class, 'create'])->name('clients.create');
    Route::post('/clients', [StaffController::class, 'store'])->name('clients.store');
    
    // Edit, update, and delete client routes
    Route::get('/clients/{client}/edit', [StaffController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [StaffController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [StaffController::class, 'destroy'])->name('clients.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';