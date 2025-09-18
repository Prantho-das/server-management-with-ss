<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class ProcessCollectAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:collect-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect processes for all push servers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting process collection for all push servers...');

        $pushServers = Server::where('connection_type', 'push')->get();

        if ($pushServers->isEmpty()) {
            $this->info('No push servers found to collect processes.');
            return Command::SUCCESS;
        }

        foreach ($pushServers as $server) {
            $this->info("Collecting processes for server: {$server->name} ({$server->ip_address})");
            $this->call('process:collect', ['server_id' => $server->id]);
        }

        $this->info('Process collection completed for all push servers.');

        return Command::SUCCESS;
    }
}
