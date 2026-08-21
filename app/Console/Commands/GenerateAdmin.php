<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('admin:generate')]
#[Description('Generate a new admin account with a random password')]
class GenerateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Admin Name');
        $email = $this->ask('Admin Email');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists.");

            return;
        }

        $password = Str::password(16);

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info('Admin account created successfully!');
        $this->line("Email: <comment>{$email}</comment>");
        $this->line("Password: <comment>{$password}</comment>");
        $this->warn('Please save this password securely as it cannot be recovered.');
    }
}
