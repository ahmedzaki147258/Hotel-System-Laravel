<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $receptionist = Role::create(['name' => 'receptionist']);
        
        // Create permissions
        // Client management permissions
        Permission::create(['name' => 'view all clients']);
        Permission::create(['name' => 'create clients']);
        Permission::create(['name' => 'update clients']);
        Permission::create(['name' => 'delete clients']);
        Permission::create(['name' => 'approve clients']);
        Permission::create(['name' => 'view own approved clients']);
        Permission::create(['name' => 'view pending clients']);
        
        // Other permissions
        Permission::create(['name' => 'manage managers']);
        Permission::create(['name' => 'manage receptionists']);
        Permission::create(['name' => 'manage all floors']);
        Permission::create(['name' => 'manage all rooms']);
        Permission::create(['name' => 'manage own receptionists']);
        Permission::create(['name' => 'manage own floors']);
        Permission::create(['name' => 'manage own rooms']);
        Permission::create(['name' => 'view client reservations']);
        Permission::create(['name' => 'make reservation']);
        
        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        
        $manager->givePermissionTo([
            'view all clients',
            'create clients',
            'update clients',
            'delete clients',
            'approve clients',
            'view own approved clients',
            'manage own receptionists',
            'manage own floors',
            'manage own rooms'
        ]);
        
        $receptionist->givePermissionTo([
            'view pending clients',
            'approve clients',
            'view own approved clients',
            'view client reservations',
            'make reservation'
        ]);
    }
}