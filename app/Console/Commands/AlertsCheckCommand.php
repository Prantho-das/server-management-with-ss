<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Alert;
use App\Models\Metric;
use App\Models\Service;
use App\Models\User; // Import User model
use App\Notifications\AlertNotification; // Import AlertNotification
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertsCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all servers and services for alert conditions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting alert checks...');

        $adminUser = User::where('role', 'admin')->first(); // Find an admin user to notify

        if (!$adminUser) {
            $this->error('No admin user found to send alerts to.');
            Log::error('No admin user found to send alerts to.');
            return Command::FAILURE;
        }

        $servers = Server::all();

        foreach ($servers as $server) {
            // Only check 'push' servers for SSH-based metrics and services
            if ($server->connection_type === 'push') {
                // Check CPU Usage
                $latestCpuMetric = $server->metrics()->where('type', 'cpu')->latest()->first();
                if ($latestCpuMetric && $latestCpuMetric->value > 80) {
                    $alert = Alert::create([
                        'server_id' => $server->id,
                        'type' => 'cpu_threshold',
                        'message' => "CPU usage on {$server->name} is high: {$latestCpuMetric->value}%",
                        'severity' => 'critical',
                        'status' => 'active',
                    ]);
                    $this->warn("Alert: CPU usage on {$server->name} is high.");
                    $adminUser->notify(new AlertNotification($alert)); // Send notification
                }

                // Check Memory Usage
                $latestMemoryMetric = $server->metrics()->where('type', 'memory')->latest()->first();
                if ($latestMemoryMetric && $latestMemoryMetric->value > 90) {
                    $alert = Alert::create([
                        'server_id' => $server->id,
                        'type' => 'memory_threshold',
                        'message' => "Memory usage on {$server->name} is high: {$latestMemoryMetric->value}%",
                        'severity' => 'critical',
                        'status' => 'active',
                    ]);
                    $this->warn("Alert: Memory usage on {$server->name} is high.");
                    $adminUser->notify(new AlertNotification($alert)); // Send notification
                }

                // Check Critical Services (example: nginx, mysql)
                $criticalServices = ['nginx', 'mysql']; // Define critical services
                foreach ($criticalServices as $serviceName) {
                    $service = $server->services()->where('name', $serviceName)->first();
                    if ($service && $service->status !== 'running') {
                        $alert = Alert::create([
                            'server_id' => $server->id,
                            'service_id' => $service->id,
                            'type' => 'service_down',
                            'message' => "Critical service '{$service->name}' on {$server->name} is {$service->status}.",
                            'severity' => 'critical',
                            'status' => 'active',
                        ]);
                        $this->warn("Alert: Critical service '{$service->name}' on {$server->name} is down.");
                        $adminUser->notify(new AlertNotification($alert)); // Send notification
                    }
                }
            }
            // For 'pull' servers, alerts would typically come from the agent via API
        }

        $this->info('Alert checks completed.');
        Log::info('Alert checks completed.');

        return Command::SUCCESS;
    }
}
