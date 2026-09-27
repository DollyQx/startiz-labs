<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            IndustrySeeder::class,
            PortfolioSeeder::class,
        ]);

        // Environment-controlled or test-only admin seeding for development/testing environments
        if (app()->environment('local', 'testing')) {
            $adminEmail = env('TEST_ADMIN_EMAIL', 'testadmin@startizlabs.local');
            $adminPassword = env('TEST_ADMIN_PASSWORD', Str::random(16));

            $admin = User::firstOrCreate(
                ['email' => strtolower($adminEmail)],
                [
                    'name' => 'Development Admin',
                    'password' => Hash::make($adminPassword),
                    'role' => UserRole::ADMIN,
                    'status' => UserStatus::ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            $clientEmail = env('TEST_CLIENT_EMAIL', 'testclient@startizlabs.local');
            $clientPassword = env('TEST_CLIENT_PASSWORD', Str::random(16));

            $client = User::firstOrCreate(
                ['email' => strtolower($clientEmail)],
                [
                    'name' => 'Development Client',
                    'password' => Hash::make($clientPassword),
                    'role' => UserRole::CLIENT,
                    'status' => UserStatus::ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            ClientProfile::firstOrCreate(
                ['user_id' => $client->id],
                [
                    'company_name' => 'Acme Technologies',
                    'contact_person' => 'Development Client',
                    'city' => 'Metropolis',
                    'country' => 'India',
                ]
            );
        }
    }
}
