<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TransactionSeeder;

class SeedUserData extends Command
{
    protected $signature = 'user:seed-data';
    protected $description = 'Seed sample data for the current user';

    public function handle()
    {
        if (!auth()->check()) {
            $this->error('No user is currently authenticated.');
            return 1;
        }

        $this->info('Seeding categories...');
        (new CategorySeeder())->run();

        $this->info('Seeding transactions...');
        (new TransactionSeeder())->run();

        $this->info('Sample data has been seeded successfully!');
        return 0;
    }
} 