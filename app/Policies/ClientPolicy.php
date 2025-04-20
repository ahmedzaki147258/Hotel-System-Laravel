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
        return $staff->hasPermissionTo('view all clients') || 
               $staff->hasPermissionTo('approve clients');
    }

    /**
     * Determine whether the staff can view own approved clients.
     */
    public function viewOwnApprovedClients(Staff $staff): bool
    {
        return $staff->hasPermissionTo('view own approved clients') || 
               $staff->hasPermissionTo('view all clients');
    }

    /**
     * Determine whether the staff can approve clients.
     */
    public function approveClient(Staff $staff): bool
    {
        return $staff->hasPermissionTo('approve clients');
    }

    /**
     * Determine whether the staff can view a specific client.
     */
    public function view(Staff $staff, Client $client): bool
    {
        if ($staff->hasPermissionTo('view all clients')) {
            return true;
        }

        if ($staff->hasPermissionTo('view own approved clients')) {
            return $client->approved_by === $staff->id;
        }

        return false;
    }

    /**
     * Determine whether the staff can create clients.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasRole(['manager', 'admin', 'receptionist']);
    }

    /**
     * Determine whether the staff can update a client.
     */
    public function update(Staff $staff, Client $client): bool
    {
        if ($staff->hasRole(['manager', 'admin'])) {
            return true;
        }

        if ($staff->hasRole('receptionist')) {
            return $client->approved_by === $staff->id;
        }

        return false;
    }

    /**
     * Determine whether the staff can delete a client.
     */
    public function delete(Staff $staff, Client $client): bool
    {
        return $staff->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the staff can restore a client.
     */
    public function restore(Staff $staff, Client $client): bool
    {
        return $staff->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the staff can permanently delete a client.
     */
    public function forceDelete(Staff $staff, Client $client): bool
    {
        return $staff->hasRole(['manager', 'admin']);
    }
}