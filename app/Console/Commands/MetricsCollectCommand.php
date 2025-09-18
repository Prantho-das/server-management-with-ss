<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Metric;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2; // Assuming phpseclib is installed

class MetricsCollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:collect {server_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect server metrics via SSH and update the database.';

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

        $this->info("Collecting metrics for server: {$server->name} ({$server->ip_address})");

        if ($server->connection_type === 'pull') {
            $this->info("Server {$server->name} is a pull system. Metric collection via SSH is not applicable.");
            Log::info("Server {$server->name} is a pull system. Metric collection via SSH is not applicable.", ['server_id' => $server->id]);
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

            $collectedMetrics = [];
            $timestamp = now();

            // Simulate metric collection
            // In real scenario: $output = $ssh->exec("top -bn1 | grep 'Cpu(s)'"); etc.
            $collectedMetrics[] = ['type' => 'cpu', 'value' => round(rand(0, 1000) / 10, 2), 'unit' => '%', 'timestamp' => $timestamp];
            $collectedMetrics[] = ['type' => 'memory', 'value' => round(rand(0, 1000) / 10, 2), 'unit' => '%', 'timestamp' => $timestamp];
            $collectedMetrics[] = ['type' => 'disk', 'value' => round(rand(0, 1000) / 10, 2), 'unit' => '%', 'timestamp' => $timestamp];
            $collectedMetrics[] = ['type' => 'network_in', 'value' => rand(100, 1000), 'unit' => 'Mbps', 'timestamp' => $timestamp];
            $collectedMetrics[] = ['type' => 'network_out', 'value' => rand(100, 1000), 'unit' => 'Mbps', 'timestamp' => $timestamp];
            $collectedMetrics[] = ['type' => 'uptime', 'value' => rand(10000, 100000), 'unit' => 'seconds', 'timestamp' => $timestamp];

            // Store metrics in the database
            foreach ($collectedMetrics as $metricData) {
                $server->metrics()->create($metricData);
            }

            $this->info("Metrics for server {$server->name} collected and updated successfully.");
            Log::info("Metrics for server {$server->name} collected and updated successfully.", ['server_id' => $server->id]);

        } catch (\Exception $e) {
            $this->error("Failed to collect metrics for server {$server->name}: " . $e->getMessage());
            Log::error("Failed to collect metrics for server {$server->name}: " . $e->getMessage(), ['server_id' => $server->id]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
