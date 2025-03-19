<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Burger;

class BurgersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $burgers = [
            [
                'name' => 'Classic Burger',
                'price' => 5000,
                'description' => 'Un délicieux burger classique avec steak, salade, tomate et sauce spéciale.',
                'stock' => 50,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Cheese Burger',
                'price' => 5500,
                'description' => 'Un burger savoureux avec du fromage fondant, steak, salade et sauce.',
                'stock' => 45,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Double Burger',
                'price' => 7000,
                'description' => 'Un burger généreux avec double steak, double fromage, salade, tomate et sauce.',
                'stock' => 30,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Chicken Burger',
                'price' => 6000,
                'description' => 'Un burger au poulet croustillant avec salade, tomate et sauce mayonnaise.',
                'stock' => 40,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Veggie Burger',
                'price' => 5500,
                'description' => 'Un burger végétarien avec galette de légumes, salade, tomate et sauce.',
                'stock' => 25,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Spicy Burger',
                'price' => 6500,
                'description' => 'Un burger épicé avec steak, piment, salade, tomate et sauce piquante.',
                'stock' => 35,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Fish Burger',
                'price' => 6000,
                'description' => 'Un burger au poisson pané avec salade et sauce tartare.',
                'stock' => 20,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Bacon Burger',
                'price' => 7000,
                'description' => 'Un burger avec steak, bacon croustillant, fromage, salade et sauce barbecue.',
                'stock' => 30,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Mega Burger',
                'price' => 8500,
                'description' => 'Notre burger le plus imposant avec triple steak, triple fromage, bacon, salade, tomate et sauce spéciale.',
                'stock' => 15,
                'is_available' => true,
                'is_archived' => false,
            ],
            [
                'name' => 'Burger du Chef',
                'price' => 7500,
                'description' => 'La création spéciale du chef avec steak, fromage de chèvre, miel, roquette et sauce balsamique.',
                'stock' => 20,
                'is_available' => true,
                'is_archived' => false,
            ],
        ];

        foreach ($burgers as $burger) {
            Burger::create($burger);
        }
    }
} 