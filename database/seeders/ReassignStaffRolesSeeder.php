<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class ReassignStaffRolesSeeder extends Seeder
{
    public function run()
    {
        
        $admin = Staff::where('email', 'admin@gmail.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }
        
        $receptionist = Staff::where('email', 'receptionist@gmail.com')->first();
        if ($receptionist) {
            $receptionist->assignRole('receptionist');
        }

        $manager = Staff::create([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        
        $manager->assignRole('manager');
    }
}