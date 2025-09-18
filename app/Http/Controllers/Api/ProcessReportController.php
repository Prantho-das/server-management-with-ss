<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessReportController extends Controller
{
    public function report(Request $request, Server $server)
    {
        // Authenticate the request using Sanctum middleware (auth:sanctum)
        // The server should send its API token in the Authorization header.

        Log::info('Process report API hit', [
            'server_id' => $server->id,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'report_data' => $request->all(), // Log all incoming data
        ]);

        $request->validate([
            'processes' => 'required|array',
            'processes.*.pid' => 'required|integer',
            'processes.*.user' => 'required|string|max:255',
            'processes.*.command' => 'required|string',
            'processes.*.cpu_percent' => 'required|numeric',
            'processes.*.memory_percent' => 'required|numeric',
            'processes.*.status' => 'required|string|in:running,sleeping,zombie,stopped,unknown',
            'processes.*.started_at' => 'nullable|date',
        ]);

        $reportedPids = [];

        foreach ($request->input('processes') as $processData) {
            Process::updateOrCreate(
                ['server_id' => $server->id, 'pid' => $processData['pid']],
                [
                    'user' => $processData['user'],
                    'command' => $processData['command'],
                    'cpu_percent' => $processData['cpu_percent'],
                    'memory_percent' => $processData['memory_percent'],
                    'status' => $processData['status'],
                    'started_at' => $processData['started_at'] ?? null,
                ]
            );
            $reportedPids[] = $processData['pid'];
        }

        // Remove processes that were not reported in this batch
        $server->processes()->whereNotIn('pid', $reportedPids)->delete();

        return response()->json([
            'message' => 'Process report received and processed successfully.',
            'server_id' => $server->id,
            'updated_processes_count' => count($reportedPids),
        ]);
    }
}
