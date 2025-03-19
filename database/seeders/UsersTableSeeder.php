<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur gestionnaire
        User::create([
            'name' => 'Admin ISI',
            'email' => 'admin@isiburger.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'address' => '123 Rue de l\'Administration, 75000 Paris',
            'phone' => '0123456789',
        ]);

        // Créer un utilisateur client
        User::create([
            'name' => 'Client Test',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'address' => '456 Avenue du Client, 75000 Paris',
            'phone' => '0987654321',
        ]);
    }
}
