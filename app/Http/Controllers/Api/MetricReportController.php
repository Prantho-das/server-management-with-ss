<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetricReportController extends Controller
{
    public function report(Request $request, Server $server)
    {
        // Authenticate the request using Sanctum middleware (auth:sanctum)
        // The server should send its API token in the Authorization header.

        Log::info('Metric report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'report_data' => $request->all(), // Log all incoming data
        ]);

        $request->validate([
            'metrics' => 'required|array',
            'metrics.*.type' => 'required|string|max:255',
            'metrics.*.value' => 'required|numeric',
            'metrics.*.unit' => 'nullable|string|max:255',
            'metrics.*.timestamp' => 'nullable|date',
        ]);

        foreach ($request->input('metrics') as $metricData) {
            $server->metrics()->create([
                'type' => $metricData['type'],
                'value' => $metricData['value'],
                'unit' => $metricData['unit'] ?? null,
                'timestamp' => $metricData['timestamp'] ?? now(),
            ]);
        }

        return response()->json([
            'message' => 'Metric report received and processed successfully.',
            'server_id' => $server->id,
            'received_metrics_count' => count($request->input('metrics')),
        ]);
    }
}

