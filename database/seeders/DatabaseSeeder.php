<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        User::create([
            'name' => 'Admin Ganteng',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'roles' => 'admin'
        ]);
    }
}
