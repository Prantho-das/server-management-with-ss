<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Metric;
use App\Models\Server;
use Carbon\Carbon;

class CpuMemoryChartWidget extends ChartWidget
{
    protected static ?string $heading = 'CPU & Memory Usage Over Time';

    protected static ?int $sort = 1;

    public ?int $selectedServerId = null; // Add this property

    protected function getType(): string
    {
        return 'line';
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

        // Fetch CPU and Memory metrics for the last 24 hours
        $cpuMetrics = Metric::where('server_id', $server->id)
            ->where('type', 'cpu')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        $memoryMetrics = Metric::where('server_id', $server->id)
            ->where('type', 'memory')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        $labels = [];
        $cpuData = [];
        $memoryData = [];

        // Group data by hour
        $dataByHour = $cpuMetrics->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('H:00');
        });

        $memoryDataByHour = $memoryMetrics->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('H:00');
        });

        for ($i = 0; $i < 24; $i++) {
            $hour = Carbon::now()->subHours(23 - $i)->format('H:00');
            $labels[] = $hour;

            $cpuValue = $dataByHour->has($hour) ? $dataByHour[$hour]->avg('value') : 0;
            $memoryValue = $memoryDataByHour->has($hour) ? $memoryDataByHour[$hour]->avg('value') : 0;

            $cpuData[] = round($cpuValue, 2);
            $memoryData[] = round($memoryValue, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'CPU Usage (%)',
                    'data' => $cpuData,
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#9BD0F5',
                ],
                [
                    'label' => 'Memory Usage (%)',
                    'data' => $memoryData,
                    'borderColor' => '#FF6384',
                    'backgroundColor' => '#FFB1C1',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
