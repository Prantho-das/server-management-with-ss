<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Server;
use App\Models\Service;
use App\Models\Alert;
use Carbon\Carbon;

class AlertSeeder extends Seeder
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

        $service = Service::where('server_id', $server->id)->first(); // Get a service for the server

        // Clear existing alerts for this server
        $server->alerts()->delete();

        // Seed some sample alerts
        Alert::create([
            'server_id' => $server->id,
            'service_id' => $service ? $service->id : null,
            'type' => 'cpu_threshold',
            'message' => 'CPU usage exceeded 85% on ' . $server->name,
            'severity' => 'critical',
            'status' => 'active',
            'triggered_at' => Carbon::now()->subMinutes(10),
        ]);

        Alert::create([
            'server_id' => $server->id,
            'service_id' => $service ? $service->id : null,
            'type' => 'memory_threshold',
            'message' => 'Memory usage exceeded 95% on ' . $server->name,
            'severity' => 'critical',
            'status' => 'active',
            'triggered_at' => Carbon::now()->subMinutes(5),
        ]);

        Alert::create([
            'server_id' => $server->id,
            'service_id' => $service ? $service->id : null,
            'type' => 'service_down',
            'message' => 'Nginx service is down on ' . $server->name,
            'severity' => 'critical',
            'status' => 'active',
            'triggered_at' => Carbon::now()->subMinutes(2),
        ]);

        Alert::create([
            'server_id' => $server->id,
            'service_id' => $service ? $service->id : null,
            'type' => 'disk_space',
            'message' => 'Disk space low on /dev/sda1 on ' . $server->name,
            'severity' => 'warning',
            'status' => 'active',
            'triggered_at' => Carbon::now()->subHours(1),
        ]);

        Alert::create([
            'server_id' => $server->id,
            'service_id' => $service ? $service->id : null,
            'type' => 'info_message',
            'message' => 'Routine check completed on ' . $server->name,
            'severity' => 'info',
            'status' => 'resolved',
            'triggered_at' => Carbon::now()->subHours(2),
            'resolved_at' => Carbon::now()->subHours(1),
        ]);

        $this->command->info('Sample alerts seeded successfully for server: ' . $server->name);
    }
}
