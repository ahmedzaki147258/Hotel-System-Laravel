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
     * Display a listing of all reservations or filtered by approval with pagination.
     */
    public function index(Request $request)
    {
        $staff = Auth::user();
        $pageSize = 5; // Fixed page size of 5
        $pageIndex = $request->input('pageIndex', 0);

        // Check authorization
        $this->authorize('viewClientReservations', Reservation::class);

        $query = null;

        if ($staff->hasPermissionTo('view all clients')) {
            // Admin and managers see all reservations
            $query = Reservation::with(['client', 'room.floor']);
        } else {
            // Receptionists see only reservations from clients they approved
            $query = Reservation::whereHas('client', function ($query) use ($staff) {
                $query->where('approved_by', $staff->id);
            })->with(['client', 'room.floor']);
        }

        // Get total count for pagination
        $total = $query->count();
        
        // Get paginated results
        $reservations = $query->latest()
            ->skip($pageIndex * $pageSize)
            ->take($pageSize)
            ->get();

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
            'pagination' => [
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'pageCount' => ceil($total / $pageSize),
                'rowCount' => $total,
            ],
            'userRole' => $staff->roles->pluck('name')[0] ?? null,
        ]);
    }
}