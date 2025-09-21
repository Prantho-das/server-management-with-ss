<?php

namespace App\Filament\Widgets;

use App\Models\Server;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalServers = Server::count();
        $activeServers = Server::where('status', 'active')->count();
        $notActiveServers = $totalServers - $activeServers;

        return [
            Stat::make('Total Servers', $totalServers),
            Stat::make('Active Servers', $activeServers),
            Stat::make('Not Active Servers', $notActiveServers),
        ];
    }
}
