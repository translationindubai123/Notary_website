<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'notary:admin';

    protected $description = 'Create an administrator account using secure interactive prompts';

    public function handle()
    {
        $name = $this->ask('Administrator name');
        $email = $this->ask('Administrator email');
        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password (minimum 12 characters)');
        $confirmation = $this->secret('Confirm password');
        if (! $password || strlen($password) < 12 || $password !== $confirmation) {
            $this->error('The password must be at least 12 characters and both entries must match.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Administrator account created.');

        return self::SUCCESS;
    }
}
