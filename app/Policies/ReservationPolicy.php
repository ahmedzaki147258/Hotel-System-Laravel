<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\Staff; 
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view client reservations.
     */
    public function viewClientReservations(Staff $staff) 
    {
        return $staff->hasPermissionTo('view client reservations');
    }
}