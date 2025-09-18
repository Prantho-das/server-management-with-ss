<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class ServerTestAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:test-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection for all servers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting connection tests for all servers...');

        $servers = Server::all();

        if ($servers->isEmpty()) {
            $this->info('No servers found to test.');
            return Command::SUCCESS;
        }

        foreach ($servers as $server) {
            $this->info("Testing connection for server: {$server->name} ({$server->ip_address})");
            $this->call('server:test', ['id' => $server->id]);
        }

        $this->info('Connection tests completed for all servers.');

        return Command::SUCCESS;
    }
}
