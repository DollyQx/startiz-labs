<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name= : The name of the admin user} {--email= : The email of the admin user} {--role=admin : The administrative role (admin or super_admin)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively and securely create an administrative user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Startiz Labs Admin Account Creation ---');

        $name = $this->option('name') ?: $this->ask('Enter Admin Name');
        $email = $this->option('email') ?: $this->ask('Enter Admin Email Address');
        $roleInput = strtolower($this->option('role') ?: $this->choice('Select Admin Role', ['admin', 'super_admin'], 0));

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'role' => $roleInput,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,super_admin'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $password = $this->secret('Enter Password (minimum 8 characters)');
        $passwordConfirm = $this->secret('Confirm Password');

        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return self::FAILURE;
        }

        $userRole = $roleInput === 'super_admin' ? UserRole::SUPER_ADMIN : UserRole::ADMIN;

        $user = User::create([
            'name' => $name,
            'email' => strtolower($email),
            'password' => Hash::make($password),
            'role' => $userRole,
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->info("Administrative account [{$user->email}] with role [{$userRole->label()}] created successfully!");

        return self::SUCCESS;
    }
}
