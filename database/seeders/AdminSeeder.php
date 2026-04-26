<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sadbank3@gmail.com'],
            [
               'name'              => 'Admin FinTrack',
               'password'          => 'admin12345',
               'role'              => 'admin',
               'status'            => 'active',
               'email_verified_at' => now(),
            ]
        );
    }
}
