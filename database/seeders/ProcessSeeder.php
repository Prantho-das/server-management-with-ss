<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Server;
use App\Models\Process;
use Carbon\Carbon;

class ProcessSeeder extends Seeder
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

        // Clear existing processes for this server
        $server->processes()->delete();

        // Seed some sample processes
        $sampleProcesses = [
            ['pid' => 1, 'user' => 'root', 'command' => '/sbin/init', 'cpu_percent' => 0.0, 'memory_percent' => 0.1, 'status' => 'running', 'started_at' => Carbon::now()->subDays(rand(1, 30))],
            ['pid' => 123, 'user' => 'www-data', 'command' => '/usr/sbin/nginx', 'cpu_percent' => 0.5, 'memory_percent' => 1.2, 'status' => 'running', 'started_at' => Carbon::now()->subHours(rand(1, 24))],
            ['pid' => 456, 'user' => 'mysql', 'command' => '/usr/sbin/mysqld', 'cpu_percent' => 1.2, 'memory_percent' => 5.0, 'status' => 'running', 'started_at' => Carbon::now()->subHours(rand(1, 24))],
            ['pid' => 789, 'user' => 'user', 'command' => 'php artisan queue:work', 'cpu_percent' => 0.1, 'memory_percent' => 0.8, 'status' => 'sleeping', 'started_at' => Carbon::now()->subMinutes(rand(1, 60))],
            ['pid' => 999, 'user' => 'root', 'command' => '/usr/bin/cron', 'cpu_percent' => 0.0, 'memory_percent' => 0.1, 'status' => 'running', 'started_at' => Carbon::now()->subDays(rand(1, 10))],
            ['pid' => 1000, 'user' => 'user', 'command' => '/usr/bin/node app.js', 'cpu_percent' => 2.5, 'memory_percent' => 3.5, 'status' => 'running', 'started_at' => Carbon::now()->subHours(rand(1, 5))],
            ['pid' => 1001, 'user' => 'user', 'command' => '/usr/bin/python script.py', 'cpu_percent' => 0.3, 'memory_percent' => 0.5, 'status' => 'sleeping', 'started_at' => Carbon::now()->subMinutes(rand(1, 30))],
        ];

        foreach ($sampleProcesses as $processData) {
            Process::updateOrCreate(
                ['server_id' => $server->id, 'pid' => $processData['pid']],
                $processData
            );
        }

        $this->command->info('Sample processes seeded successfully for server: ' . $server->name);
    }
}
