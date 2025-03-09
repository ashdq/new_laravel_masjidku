<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        
        // Buat 1 admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'roles' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'pengurus',
            'email' => 'pengurus@pengurus.com',
            'password' => Hash::make('password123'),
            'roles' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat 8 user warga
        for ($i = 0; $i < 8; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'roles' => 'warga',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
