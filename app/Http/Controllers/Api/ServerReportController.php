<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServerReportController extends Controller
{
    public function report(Request $request, Server $server)
    {
        // Log the incoming report
        Log::info('Server report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'report_data' => $request->all(), // Log all incoming data
        ]);

        // Update server information based on the report data
        // For demonstration, let's assume the report contains 'os', 'cpu', 'ram', 'disk', 'status'
        $server->update([
            'os' => $request->input('os', $server->os),
            'cpu' => $request->input('cpu', $server->cpu),
            'ram' => $request->input('ram', $server->ram),
            'disk' => $request->input('disk', $server->disk),
            'status' => $request->input('status', 'online'), // Assume online if reporting
        ]);

        // Generate a new API token for the server
        // First, delete any existing tokens for this server to ensure only one active token
        $server->tokens()->delete();
        $token = $server->createToken('server-api-token')->plainTextToken;

        return response()->json([
            'message' => 'Server report received and token generated.',
            'server' => [
                'id' => $server->id,
                'name' => $server->name,
                'ip_address' => $server->ip_address,
                'hostname' => $server->hostname,
                'os' => $server->os,
                'cpu' => $server->cpu,
                'ram' => $server->ram,
                'disk' => $server->disk,
                'status' => $server->status,
                'updated_at' => $server->updated_at,
            ],
            'api_token' => $token, // Return the new API token
        ]);
    }
}
