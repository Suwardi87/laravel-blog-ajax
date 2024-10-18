<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::Create([
            'name' => 'Admin',
            'email' => 'suwardyser87@gmail.com',
            'password' => bcrypt('password'),
        ]);

        Category::factory(50000)->create();
    }

}
