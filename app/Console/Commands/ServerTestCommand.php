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

        $this->info("Fetching info for server: {$server->name} ({$server->ip_address})");

        // Placeholder for SSH connection and command execution
        // In a real application, you would use phpseclib or a similar library
        // and handle SSH credentials securely.
        try {
            // Example using phpseclib (requires installation: composer require phpseclib/phpseclib)
            // $ssh = new SSH2($server->ip_address);
            // if (!$ssh->login($server->ssh_user, $server->ssh_password)) { // Assuming ssh_user and ssh_password are in ssh_details or separate fields
            //     throw new \Exception('SSH Login Failed');
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

            $this->info("Server {$server->name} info updated successfully.");
            Log::info("Server {$server->name} info updated successfully.", ['server_id' => $server->id]);

        } catch (\Exception $e) {
            $this->error("Failed to fetch info for server {$server->name}: " . $e->getMessage());
            $server->update(['status' => 'offline']);
            Log::error("Failed to fetch info for server {$server->name}: " . $e->getMessage(), ['server_id' => $server->id]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
