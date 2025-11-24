<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@verves.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@verves.com',
                'password' => Hash::make('password'), // Change this in production!
                'phone' => '+1234567890',
                'role' => 'admin',
                'type' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Sample Coach
        User::firstOrCreate(
            ['email' => 'coach@verves.com'],
            [
                'name' => 'John Coach',
                'email' => 'coach@verves.com',
                'password' => Hash::make('password'), // Change this in production!
                'phone' => '+1234567891',
                'role' => 'user',
                'type' => 'coach',
                'bio' => 'Experienced fitness coach with 10+ years of experience.',
                'specialization' => 'Strength Training, Weight Loss',
                'email_verified_at' => now(),
            ]
        );

        // Create Sample Client
        User::firstOrCreate(
            ['email' => 'client@verves.com'],
            [
                'name' => 'Jane Client',
                'email' => 'client@verves.com',
                'password' => Hash::make('password'), // Change this in production!
                'phone' => '+1234567892',
                'role' => 'user',
                'type' => 'client',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@verves.com / password');
        $this->command->info('Coach: coach@verves.com / password');
        $this->command->info('Client: client@verves.com / password');
    }
}
