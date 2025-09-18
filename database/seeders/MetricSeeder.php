<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Server;
use App\Models\Metric;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $server = Server::first(); // Get the first server

        if (!$server) {
            $this->command->info('No servers found. Please create a server first.');
            return;
        }

        // Clear existing metrics for this server
        $server->metrics()->delete();

        // Seed some sample CPU metrics over time
        for ($i = 0; $i < 24; $i++) { // 24 data points for 24 hours
            Metric::create([
                'server_id' => $server->id,
                'type' => 'cpu',
                'value' => rand(10, 90) + (rand(0, 99) / 100), // Random float between 10 and 90
                'unit' => '%',
                'timestamp' => now()->subHours(23 - $i),
            ]);
            Metric::create([
                'server_id' => $server->id,
                'type' => 'memory',
                'value' => rand(20, 80) + (rand(0, 99) / 100), // Random float between 20 and 80
                'unit' => '%',
                'timestamp' => now()->subHours(23 - $i),
            ]);
        }

        // Seed some disk usage metrics
        Metric::create([
            'server_id' => $server->id,
            'type' => 'disk_root',
            'value' => rand(30, 70) + (rand(0, 99) / 100),
            'unit' => '%',
            'timestamp' => now(),
        ]);
        Metric::create([
            'server_id' => $server->id,
            'type' => 'disk_data',
            'value' => rand(10, 50) + (rand(0, 99) / 100),
            'unit' => '%',
            'timestamp' => now(),
        ]);

        $this->command->info('Sample metrics seeded successfully for server: ' . $server->name);
    }
}
