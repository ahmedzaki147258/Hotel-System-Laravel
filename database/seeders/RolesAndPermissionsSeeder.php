<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $receptionist = Role::create(['name' => 'receptionist']);

        // Create permission
        // Admin permissions
        Permission::create(['name' => 'manage managers']);
        Permission::create(['name' => 'manage receptionists']);
        Permission::create(['name' => 'manage clients']);
        Permission::create(['name' => 'manage all floors']);
        Permission::create(['name' => 'manage all rooms']);

        // Manager permissions
        Permission::create(['name' => 'manage own receptionists']);
        Permission::create(['name' => 'manage own floors']);
        Permission::create(['name' => 'manage own rooms']);
        Permission::create(['name' => 'view all clients']);

        // Receptionist permissions
        Permission::create(['name' => 'approve clients']);
        Permission::create(['name' => 'view own approved clients']);
        Permission::create(['name' => 'view client reservations']);
        Permission::create(['name' => 'make reservation']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'manage own receptionists',
            'manage own floors',
            'manage own rooms',
            'view all clients'
        ]);

        $receptionist->givePermissionTo([
            'approve clients',
            'view own approved clients',
            'view client reservations'
        ]);
    }
}
