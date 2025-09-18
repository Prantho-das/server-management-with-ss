<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Metric;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Str; // Import Str facade

class DiskUsageChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Disk Usage';

    protected static ?int $sort = 2;

    public ?int $selectedServerId = null; // Add this property

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $server = Server::find($this->selectedServerId); // Use selectedServerId

        if (!$server) {
            return [
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $diskMetrics = Metric::where('server_id', $server->id)
            ->where('type', 'like', 'disk_%') // Assuming disk metrics are like 'disk_root', 'disk_data'
            ->orderBy('timestamp', 'desc')
            ->get()
            ->unique('type'); // Get the latest metric for each disk type

        $labels = $diskMetrics->pluck('type')->map(fn ($type) => Str::after($type, 'disk_'))->toArray();
        $data = $diskMetrics->pluck('value')->toArray();
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']; // Example colors

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
