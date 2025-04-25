<?php

use App\Mail\InactiveClientNotification;
use App\Models\Client;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('clients:inactive', function () {
    $inactiveClients = Client::where('last_login_at', '<', Carbon::now()->subMonth())->get();
    foreach ($inactiveClients as $client) {
        Mail::to($client->email)->send(new InactiveClientNotification($client));
        $client->last_login_at = null;
        $client->save();
    }
})
    ->purpose("send an email notification to clients who didn't login from the past month")
    ->dailyAt('00:00');

Artisan::command('reservations:check', function () {
    $reservations = Reservation::where('check_out_at', '<', now())->get();
    foreach ($reservations as $reservation) {
        $room = $reservation->room;
        $room->status = 'available';
        $room->save();
    }
})
    ->purpose("check if the reservation is completed and update the room status to available")
    ->dailyAt('00:02');
