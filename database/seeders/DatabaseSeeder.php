<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::factory()->admin()->create([
            'name' => 'Admin CTF',
            'email' => 'admin@undiksha.ac.id',
            'password' => bcrypt('password'),
        ]);

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
