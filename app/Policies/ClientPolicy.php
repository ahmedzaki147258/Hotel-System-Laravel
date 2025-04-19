<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Staff;

class ClientPolicy
{
    /**
     * Determine whether the staff can view any clients.
     */
    public function viewAny(Staff $staff): bool
    {

        return $staff->hasRole(['manager', 'receptionist', 'admin']);
    }

    /**
     * Determine whether the staff can view a specific client.
     */
    public function view(Staff $staff, Client $client): bool
    {

        if ($staff->hasRole('manager')) {
            return true;
        }

        if ($staff->hasRole('receptionist')) {
            return $client->approved_by === $staff->id;
        }

        return false;
    }

    /**
     * Determine whether the staff can create clients.
     * Managers only.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasRole(['manager', 'receptionist', 'admin']);
    }

    /**
     * Determine whether the staff can update a client.
     * Managers only.
     */
    public function update(Staff $staff, Client $client): bool
    {
        return $staff->hasRole('manager');
    }

    /**
     * Determine whether the staff can delete a client.
     * Managers only.
     */
    public function delete(Staff $staff, Client $client): bool
    {
        return $staff->hasRole('manager');
    }

    /**
     * Determine whether the staff can restore a client.
     * Managers only.
     */
    public function restore(Staff $staff, Client $client): bool
    {
        return $staff->hasRole('manager');
    }

    /**
     * Determine whether the staff can permanently delete a client.
     * Managers only.
     */
    public function forceDelete(Staff $staff, Client $client): bool
    {
        return $staff->hasRole('manager');
    }
}
