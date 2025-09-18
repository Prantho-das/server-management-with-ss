<?php

use App\Http\Controllers\Api\ServerReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/servers/{server}/report', [ServerReportController::class, 'report']);
Route::post('/servers/{server}/services/report', [ServiceReportController::class, 'report'])->middleware('auth:sanctum');
Route::post('/servers/{server}/processes/report', [ProcessReportController::class, 'report'])->middleware('auth:sanctum');
Route::post('/servers/{server}/metrics/report', [MetricReportController::class, 'report'])->middleware('auth:sanctum');
Route::post('/servers/{server}/alerts/report', [AlertReportController::class, 'report'])->middleware('auth:sanctum');
