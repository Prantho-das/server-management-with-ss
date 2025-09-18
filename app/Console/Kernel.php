<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Server; // Import the Server model

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the server:test command for all servers every 5 minutes
        $schedule->call(function () {
            $servers = Server::all();
            foreach ($servers as $server) {
                $this->call('server:test', ['id' => $server->id]);
            }
        })->everyFiveMinutes();

        // Schedule the service:scan command for 'push' servers every 5 minutes
        $schedule->call(function () {
            $pushServers = Server::where('connection_type', 'push')->get();
            foreach ($pushServers as $server) {
                $this->call('service:scan', ['server_id' => $server->id]);
            }
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
