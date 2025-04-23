<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ReservationController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of all reservations or filtered by approval.
     */
    public function index()
    {
        $staff = Auth::user();
        $reservations = [];

        // Check authorization
        $this->authorize('viewClientReservations', Reservation::class);

        if ($staff->hasPermissionTo('view all clients')) {
            // Admin and managers see all reservations
            $reservations = Reservation::with(['client', 'room.floor'])->get();
        } else {
            // Receptionists see only reservations from clients they approved
            $reservations = Reservation::whereHas('client', function ($query) use ($staff) {
                $query->where('approved_by', $staff->id);
            })->with(['client', 'room.floor'])->get();
        }

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'client_name' => $reservation->client->name,
                    'room_number' => $reservation->room->number,
                    'floor_number' => $reservation->room->floor->number,
                    'accompany_number' => $reservation->accompany_number,
                    'paid_price' => $reservation->paid_price_in_cents / 100, // Convert cents to dollars/currency
                    'check_out_at' => $reservation->check_out_at,
                    'created_at' => $reservation->created_at,
                ];
            }),
            'userRole' => $staff->roles->pluck('name')[0] ?? null,
        ]);
    }
}