<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class ServiceScanAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:scan-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan services for all push servers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting service scans for all push servers...');

        $pushServers = Server::where('connection_type', 'push')->get();

        if ($pushServers->isEmpty()) {
            $this->info('No push servers found to scan services.');
            return Command::SUCCESS;
        }

        foreach ($pushServers as $server) {
            $this->info("Scanning services for server: {$server->name} ({$server->ip_address})");
            $this->call('service:scan', ['server_id' => $server->id]);
        }

        $this->info('Service scans completed for all push servers.');

        return Command::SUCCESS;
    }
}
