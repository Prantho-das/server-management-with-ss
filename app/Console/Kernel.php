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
        // Schedule commands to run every 5 minutes
        $schedule->command('server:test-all')->everyFiveMinutes();
        $schedule->command('service:scan-all')->everyFiveMinutes();
        $schedule->command('process:collect-all')->everyFiveMinutes();
        $schedule->command('metrics:collect-all')->everyFiveMinutes();
        $schedule->command('alerts:check')->everyFiveMinutes();
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
