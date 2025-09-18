<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Service;
use App\Models\Server;
use Carbon\Carbon; // Import Carbon for consistency, though not strictly needed here

class ServiceStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Service Status Overview';

    protected static ?int $sort = 3;

    public ?int $selectedServerId = null; // Add this property

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $server = Server::find($this->selectedServerId); // Use selectedServerId

        if (!$server) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $serviceStatusCounts = Service::where('server_id', $server->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $labels = $serviceStatusCounts->keys()->toArray();
        $data = $serviceStatusCounts->values()->toArray();

        $backgroundColors = [];
        foreach ($labels as $status) {
            if ($status === 'running') {
                $backgroundColors[] = '#4CAF50'; // Green
            } elseif ($status === 'stopped') {
                $backgroundColors[] = '#F44336'; // Red
            } else {
                $backgroundColors[] = '#FFC107'; // Amber
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Services',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
