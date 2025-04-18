<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin {--name= : The email of the admin} {--password= : The password of the admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $email = $this->option('name');
        $password = $this->option('password');

        if (!$email || !$password){
            $this->error('Email and password are required');
            return 1;
        }

        if (Staff::where('email', $email)->exists()){
            $this->error("Admin with email $email already exists");
            return 1;
        }

        $admin = Staff::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $admin->assignRole('admin');

        $this->info("Admin user created successfully with email: $email");
        return 0;
    }
}
