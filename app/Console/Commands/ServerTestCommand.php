<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2; // Assuming phpseclib is installed

class ServerTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:test {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch server info via SSH and update the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serverId = $this->argument('id');
        $server = Server::find($serverId);

        if (!$server) {
            $this->error("Server with ID {$serverId} not found.");
            return Command::FAILURE;
        }

        $this->info("Processing server: {$server->name} ({$server->ip_address}) - Connection Type: {$server->connection_type}");

        if ($server->connection_type === 'pull') {
            $this->info("Server {$server->name} is a pull system. No SSH connection required for this command.");
            Log::info("Server {$server->name} is a pull system. No SSH connection required for this command.", ['server_id' => $server->id]);
            // Optionally, you might want to update its status to 'online' if it's expected to report via API
            // $server->update(['status' => 'online']);
            return Command::SUCCESS;
        }

        // For 'push' system, attempt SSH connection
        if (empty($server->ssh_username)) {
            $this->error("SSH username is not configured for server {$server->name}.");
            $server->update(['status' => 'offline']);
            return Command::FAILURE;
        }

        try {
            // Example using phpseclib (requires installation: composer require phpseclib/phpseclib)
            // $ssh = new SSH2($server->ip_address, $server->ssh_port);

            // if (!empty($server->ssh_password)) {
            //     if (!$ssh->login($server->ssh_username, $server->ssh_password)) {
            //         throw new \Exception('SSH Password Login Failed');
            //     }
            // } elseif (!empty($server->ssh_private_key)) {
            //     $key = new \phpseclib3\Crypt\RSA();
            //     $key->load($server->ssh_private_key);
            //     if (!$ssh->login($server->ssh_username, $key)) {
            //         throw new \Exception('SSH Private Key Login Failed');
            //     }
            // } else {
            //     throw new \Exception('No SSH credentials provided (password or private key).');
            // }

            // For demonstration, simulate fetching data
            $os = 'Ubuntu 22.04 LTS';
            $cpu = 'Intel Xeon E3-1505M v5';
            $ram = '32GB';
            $disk = '500GB SSD';
            $status = 'online';

            // Update server information
            $server->update([
                'os' => $os,
                'cpu' => $cpu,
                'ram' => $ram,
                'disk' => $disk,
                'status' => $status,
            ]);

            $this->info("Server {$server->name} info updated successfully via SSH.");
            Log::info("Server {$server->name} info updated successfully via SSH.", ['server_id' => $server->id]);

        } catch (\Exception $e) {
            $this->error("Failed to fetch info for server {$server->name}: " . $e->getMessage());
            $server->update(['status' => 'offline']);
            Log::error("Failed to fetch info for server {$server->name}: " . $e->getMessage(), ['server_id' => $server->id]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
