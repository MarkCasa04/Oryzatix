<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Account
        User::firstOrCreate(
            ['email' => 'admin@oryzatix.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'location' => 'JPC IT Research Lab',
            ]
        );

        // 2. Staff / Agricultural Extension Worker
        User::firstOrCreate(
            ['email' => 'staff@oryzatix.ph'],
            [
                'name' => 'Maria Santos (Agronomist)',
                'password' => Hash::make('password'),
                'role' => 'agri_worker',
                'location' => 'DA Regional Extension Office',
            ]
        );

        // 3. Registered Farmers
        User::firstOrCreate(
            ['email' => 'farmer@oryzatix.ph'],
            [
                'name' => 'Mang Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'location' => 'Brgy. San Mariano, Roxas, Oriental Mindoro',
            ]
        );

        User::firstOrCreate(
            ['email' => 'pedro@oryzatix.ph'],
            [
                'name' => 'Pedro Penduko',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'location' => 'Brgy. Cantil, Roxas, Oriental Mindoro',
            ]
        );
    }
}
