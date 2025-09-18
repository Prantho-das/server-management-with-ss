<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class MetricsCollectAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:collect-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect metrics for all push servers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting metrics collection for all push servers...');

        $pushServers = Server::where('connection_type', 'push')->get();

        if ($pushServers->isEmpty()) {
            $this->info('No push servers found to collect metrics.');
            return Command::SUCCESS;
        }

        foreach ($pushServers as $server) {
            $this->info("Collecting metrics for server: {$server->name} ({$server->ip_address})");
            $this->call('metrics:collect', ['server_id' => $server->id]);
        }

        $this->info('Metrics collection completed for all push servers.');

        return Command::SUCCESS;
    }
}
