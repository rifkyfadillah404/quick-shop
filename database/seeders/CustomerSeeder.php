<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'customer@quickshop.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1234567890',
            'address' => '123 Main Street, City, State 12345',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1987654321',
            'address' => '456 Oak Avenue, City, State 67890',
            'email_verified_at' => now(),
        ]);
    }
}
