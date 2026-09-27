<?php

namespace Database\Seeders;

use App\Services\PortfolioService;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Seed Startiz Labs 5 portfolio case studies.
     */
    public function run(): void
    {
        // Verified array of official portfolio projects ready for public rendering
        $projects = PortfolioService::getProjects();
        // Additional database sync logic can be executed here if portfolio table is added in future
    }
}
