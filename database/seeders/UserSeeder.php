<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin', // make sure you have role column
        ]);

        // Regular User
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
        ]);
    }
}
