<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;

class ReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receptionist = Staff::create([
            'name' => 'Receptionist',
            'email' => 'receptionist@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        
        $receptionist->assignRole('receptionist');
    }
}
