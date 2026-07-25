<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::updateOrCreate(
            ['email' => 'admin@undiksha.ac.id'],
            [
                'name' => 'Admin CTF',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Categories
        $categoryNames = ['Web Exploitation', 'Reverse Engineering', 'Cryptography', 'Forensics', 'Miscellaneous'];
        foreach ($categoryNames as $name) {
            Category::firstOrCreate([
                'name' => $name,
            ], [
                'description' => "Tantangan kategori $name",
            ]);
        }
    }
}
