<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@antoniobc.net'],
            [
                'name' => 'Antonio Benalcázar',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->call(PortfolioSeeder::class);
    }
}
