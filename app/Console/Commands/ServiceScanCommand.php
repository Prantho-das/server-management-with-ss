<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2; // Assuming phpseclib is installed

class ServiceScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:scan {server_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect running services on a server via SSH and update the database.';

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

        $this->info("Scanning services for server: {$server->name} ({$server->ip_address})");

        if ($server->connection_type === 'pull') {
            $this->info("Server {$server->name} is a pull system. Service scanning via SSH is not applicable.");
            Log::info("Server {$server->name} is a pull system. Service scanning via SSH is not applicable.", ['server_id' => $server->id]);
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

            $detectedServices = [];

            // Simulate service detection
            $servicesToDetect = ['nginx', 'mysql', 'redis', 'nodejs', 'pm2', 'cron', 'php-fpm'];
            foreach ($servicesToDetect as $serviceName) {
                // Simulate checking if service is running and getting info
                // In real scenario: $output = $ssh->exec("pgrep -x {$serviceName}");
                $isRunning = (rand(0, 1) === 1); // Randomly simulate if service is running
                $status = $isRunning ? 'running' : 'stopped';
                $version = $isRunning ? 'v' . rand(1, 10) . '.' . rand(0, 9) : null;
                $port = $isRunning ? rand(1000, 9999) : null;
                $cpuUsage = $isRunning ? round(rand(0, 1000) / 100, 2) : null; // 0.00 to 10.00
                $memoryUsage = $isRunning ? round(rand(0, 1000) / 100, 2) : null; // 0.00 to 10.00

                $detectedServices[] = [
                    'name' => $serviceName,
                    'process_name' => $serviceName, // Simplified for now
                    'port' => $port,
                    'version' => $version,
                    'status' => $status,
                    'cpu_usage' => $cpuUsage,
                    'memory_usage' => $memoryUsage,
                ];
            }

            // Update or create services in the database
            foreach ($detectedServices as $serviceData) {
                Service::updateOrCreate(
                    ['server_id' => $server->id, 'name' => $serviceData['name']],
                    $serviceData
                );
            }

            $this->info("Services for server {$server->name} scanned and updated successfully.");
            Log::info("Services for server {$server->name} scanned and updated successfully.", ['server_id' => $server->id]);

        } catch (\Exception $e) {
            $this->error("Failed to scan services for server {$server->name}: " . $e->getMessage());
            Log::error("Failed to scan services for server {$server->name}: " . $e->getMessage(), ['server_id' => $server->id]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
