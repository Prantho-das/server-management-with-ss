<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Alert;
use App\Models\Service; // Needed to find service_id if reported by name
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlertReportController extends Controller
{
    public function report(Request $request, Server $server)
    {
        // Authenticate the request using Sanctum middleware (auth:sanctum)
        // The server should send its API token in the Authorization header.

        Log::info('Alert report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'report_data' => $request->all(), // Log all incoming data
        ]);

        $request->validate([
            'alerts' => 'required|array',
            'alerts.*.type' => 'required|string|max:255',
            'alerts.*.message' => 'required|string',
            'alerts.*.severity' => 'required|string|in:info,warning,critical',
            'alerts.*.status' => 'required|string|in:active,resolved,ignored',
            'alerts.*.triggered_at' => 'nullable|date',
            'alerts.*.service_name' => 'nullable|string|max:255', // Optional: if service is reported by name
        ]);

        foreach ($request->input('alerts') as $alertData) {
            $serviceId = null;
            if (isset($alertData['service_name'])) {
                $service = Service::where('server_id', $server->id)
                                  ->where('name', $alertData['service_name'])
                                  ->first();
                if ($service) {
                    $serviceId = $service->id;
                }
            }

            Alert::create([
                'server_id' => $server->id,
                'service_id' => $serviceId,
                'type' => $alertData['type'],
                'message' => $alertData['message'],
                'severity' => $alertData['severity'],
                'status' => $alertData['status'],
                'triggered_at' => $alertData['triggered_at'] ?? now(),
                'resolved_at' => $alertData['resolved_at'] ?? null,
                'ignored_at' => $alertData['ignored_at'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Alert report received and processed successfully.',
            'server_id' => $server->id,
            'received_alerts_count' => count($request->input('alerts')),
        ]);
    }
}
