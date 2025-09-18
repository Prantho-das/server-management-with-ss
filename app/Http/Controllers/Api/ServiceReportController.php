<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceReportController extends Controller
{
    public function report(Request $request, Server $server)
    {
        // Authenticate the request using Sanctum middleware (auth:sanctum)
        // The server should send its API token in the Authorization header.

        Log::info('Service report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'report_data' => $request->all(), // Log all incoming data
        ]);

        $request->validate([
            'services' => 'required|array',
            'services.*.name' => 'required|string|max:255',
            'services.*.process_name' => 'nullable|string|max:255',
            'services.*.port' => 'nullable|integer',
            'services.*.version' => 'nullable|string|max:255',
            'services.*.status' => 'required|string|in:running,stopped,unknown',
            'services.*.cpu_usage' => 'nullable|numeric',
            'services.*.memory_usage' => 'nullable|numeric',
        ]);

        $reportedServiceNames = [];

        foreach ($request->input('services') as $serviceData) {
            Service::updateOrCreate(
                ['server_id' => $server->id, 'name' => $serviceData['name']],
                [
                    'process_name' => $serviceData['process_name'] ?? null,
                    'port' => $serviceData['port'] ?? null,
                    'version' => $serviceData['version'] ?? null,
                    'status' => $serviceData['status'],
                    'cpu_usage' => $serviceData['cpu_usage'] ?? null,
                    'memory_usage' => $serviceData['memory_usage'] ?? null,
                ]
            );
            $reportedServiceNames[] = $serviceData['name'];
        }

        // Remove services that were not reported in this batch
        $server->services()->whereNotIn('name', $reportedServiceNames)->delete();

        $server->update(['status' => 'online']); // Assume server is online if it's reporting services

        return response()->json([
            'message' => 'Service report received and processed successfully.',
            'server_id' => $server->id,
            'updated_services_count' => count($reportedServiceNames),
        ]);
    }
}

