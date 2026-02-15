<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create 
                            {name : Display name of the user} 
                            {email : Email address} 
                            {password? : Password (will prompt if not provided)}';

    protected $description = 'Create or update a user and assign the admin role';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password') ?? $this->secret('Password');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $user->syncRoles(['admin']);

        $this->info("Admin user created/updated: {$user->email} (name: {$user->name})");
        return self::SUCCESS;
    }
}
