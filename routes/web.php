<?php

use App\Helpers\CountryHelper;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\IsAdminOrManager;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ReservationController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Client dashboard routes
Route::middleware(['auth:sanctum', 'auth:client'])->group(function () {
    Route::get('client/dashboard', function () {
        return Inertia::render('ClientDashboard', [
            'countries' => CountryHelper::getVisibleCountries()
        ]);
    })->name('client.dashboard');

    Route::get('client/make-reservation', function () {
        return Inertia::render('ClientDashboard', [
            'countries' => CountryHelper::getVisibleCountries()
        ]);
    })->name('client.make.reservation');

    Route::get('client/my-reservations', function () {
        return Inertia::render('ClientDashboard', [
            'countries' => CountryHelper::getVisibleCountries()
        ]);
    })->name('client.my.reservations');

    Route::get('client/profile', function () {
        return Inertia::render('ClientDashboard', [
            'countries' => CountryHelper::getVisibleCountries()
        ]);
    })->name('client.profile');

    Route::get('client/appearance', function () {
        return Inertia::render('ClientDashboard', [
            'countries' => CountryHelper::getVisibleCountries()
        ]);
    })->name('client.appearance');
});

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

// payment routes
Route::middleware(['auth:sanctum', 'auth:client'])->group(function () {
    Route::get('/reservation/payment', [StripeController::class, 'reservationPayment'])->name('stripe.reservation.payment');
    Route::get('/reservation/success', [StripeController::class, 'reservationSuccess'])->name('stripe.reservation.success');
    Route::get('/client/store-reservation', [ClientController::class, 'storeReservation'])->middleware(['auth:sanctum', 'auth:client'])->name('client.store.reservation');
});


Route::middleware(['auth'])->group(function () {
    // Client management routes
    Route::resource('clients', ClientManagementController::class);
    Route::get('/my-approved-clients', [ClientManagementController::class, 'myApprovedClients'])->name('clients.approved');
    Route::post('/clients/{client}/approve', [ClientManagementController::class, 'approve'])->name('clients.approve');

    //receptionists
});

Route::middleware(['auth', IsAdminOrManager::class])->group(function () {
    Route::post('receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');
    // Receptionists management routes
    Route::resource('receptionists', ReceptionistController::class);
    Route::post('/receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('receptionists.ban');
    Route::post('/receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('receptionists.unban');

    // Managers management routes
    Route::resource('managers', ManagerController::class);
    Route::post('/managers/{manager}/ban', [ManagerController::class, 'ban'])->name('managers.ban');
    Route::post('/managers/{manager}/unban', [ManagerController::class, 'unban'])->name('managers.unban');
});


Route::middleware(['auth', IsAdminOrManager::class])->group(function () {
    Route::resource('floors', FloorController::class);
    Route::resource('rooms', RoomController::class);
});

// Reservation routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
});

Route::get('clients-export', [DashboardController::class, 'export'])->name('clients.export');
Route::get('dashboard-statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
