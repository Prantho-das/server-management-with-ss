<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2; // Assuming phpseclib is installed

class ProcessCollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:collect {server_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect running processes on a server via SSH and update the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serverId = $this->argument('server_id');
        $server = Server::find($serverId);

        if (!$server) {
            $this->error("Server with ID {$serverId} not found.");
            return Command::FAILURE;
        }

        $this->info("Collecting processes for server: {$server->name} ({$server->ip_address})");

        if ($server->connection_type === 'pull') {
            $this->info("Server {$server->name} is a pull system. Process collection via SSH is not applicable.");
            Log::info("Server {$server->name} is a pull system. Process collection via SSH is not applicable.", ['server_id' => $server->id]);
            return Command::SUCCESS;
        }

        if (empty($server->ssh_username)) {
            $this->error("SSH username is not configured for server {$server->name}.");
            return Command::FAILURE;
        }

        try {
            // Establish SSH connection (simulated)
            // In a real scenario, you would use phpseclib to connect
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

            $collectedProcesses = [];

            // Simulate process collection
            // In real scenario: $output = $ssh->exec("ps aux");
            // Parse $output to get process details
            $sampleProcesses = [
                ['pid' => 1, 'user' => 'root', 'command' => '/sbin/init', 'cpu_percent' => 0.0, 'memory_percent' => 0.1, 'status' => 'running', 'started_at' => now()->subDays(rand(1, 30))],
                ['pid' => 123, 'user' => 'www-data', 'command' => '/usr/sbin/nginx', 'cpu_percent' => 0.5, 'memory_percent' => 1.2, 'status' => 'running', 'started_at' => now()->subHours(rand(1, 24))],
                ['pid' => 456, 'user' => 'mysql', 'command' => '/usr/sbin/mysqld', 'cpu_percent' => 1.2, 'memory_percent' => 5.0, 'status' => 'running', 'started_at' => now()->subHours(rand(1, 24))],
                ['pid' => 789, 'user' => 'user', 'command' => 'php artisan queue:work', 'cpu_percent' => 0.1, 'memory_percent' => 0.8, 'status' => 'sleeping', 'started_at' => now()->subMinutes(rand(1, 60))],
            ];

            foreach ($sampleProcesses as $processData) {
                $collectedProcesses[] = $processData;
            }

            // Update or create processes in the database
            foreach ($collectedProcesses as $processData) {
                Process::updateOrCreate(
                    ['server_id' => $server->id, 'pid' => $processData['pid']],
                    $processData
                );
            }

            // Remove processes that are no longer running (not in the collected list)
            $server->processes()->whereNotIn('pid', array_column($collectedProcesses, 'pid'))->delete();


            $this->info("Processes for server {$server->name} collected and updated successfully.");
            Log::info("Processes for server {$server->name} collected and updated successfully.", ['server_id' => $server->id]);

        } catch (\Exception $e) {
            $this->error("Failed to collect processes for server {$server->name}: " . $e->getMessage());
            Log::error("Failed to collect processes for server {$server->name}: " . $e->getMessage(), ['server_id' => $server->id]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
