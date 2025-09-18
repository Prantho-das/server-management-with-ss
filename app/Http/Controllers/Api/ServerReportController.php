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
        // Authenticate the request using Sanctum (middleware will handle this)
        // For now, we'll just log the request and return the server data.

        Log::info('Server report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // In a real scenario, you might want to update server status or other details
        // based on the report received from the server.
        // For this task, we are just returning the server's current data.

        return response()->json([
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
        ]);
    }
}
