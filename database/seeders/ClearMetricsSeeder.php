<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Metric; // Import the Metric model

class ClearMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Metric::truncate(); // Delete all records from the metrics table

        $this->command->info('All metrics data cleared successfully!');
    }
}
