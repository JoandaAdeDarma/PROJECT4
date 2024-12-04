<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
 
        User::create([
            'name' => 'user1',
            'email' => 'user01@gmail.com',
            'password' => bcrypt('123456789'),
            'point' => 10000,
        ]);

        Admin::create([
            'name' => 'admin',
            'username' => 'Admin',
            'email' => 'admin01@gmail.com',
            'password' => bcrypt('123456789'),
        ]);

        // Contoh untuk membuat beberapa pengguna secara otomatis (opsional)
        // User::factory(10)->create();
    }
}
