<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClearExistingRolesAndPermissions extends Migration
{
    public function up()
    {
        // Clear existing roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Delete all roles and permissions
        Permission::query()->delete();
        Role::query()->delete();
    }

    public function down()
    {
        // This cannot be undone
    }
}