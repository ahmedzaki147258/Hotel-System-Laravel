<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // For fresh start
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create permissions
        $permissions = [
            // Client management permissions
            'view all clients',
            'create clients',
            'update clients',
            'delete clients',
            'approve clients',
            'view own approved clients',
            'view pending clients',

            // Other permissions
            'manage managers',
            'manage receptionists',
            'manage all floors',
            'manage all rooms',
            'manage own receptionists',
            'manage own floors',
            'manage own rooms',
            'view client reservations',
            'make reservation',
        ];

        // Create each permission
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $receptionist = Role::create(['name' => 'receptionist', 'guard_name' => 'web']);

        // Give all permissions to admin
        $admin->givePermissionTo(Permission::all());

        // Give specific permissions to manager
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

        // Give specific permissions to receptionist
        $receptionist->givePermissionTo([
            'view pending clients',
            'approve clients',
            'view own approved clients',
            'view client reservations',
            'make reservation'
        ]);
    }
}