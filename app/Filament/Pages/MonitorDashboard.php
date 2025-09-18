<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Filament\Widgets\CpuMemoryChartWidget; // Import the CPU/Memory chart widget
use App\Filament\Widgets\DiskUsageChartWidget; // Import the Disk Usage chart widget
use App\Filament\Widgets\ServiceStatusChartWidget; // Import the Service Status chart widget
use Filament\Forms\Components\Select; // Import Select component
use Filament\Forms\Form; // Import Form class
use App\Models\Server; // Import Server model

class MonitorDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.monitor-dashboard';

    protected static ?string $title = 'Monitoring Dashboard';

    public ?int $selectedServerId = null; // Property to hold the selected server ID

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin() || Auth::user()->isDevOps();
    }

    public function mount(): void
    {
        // Set a default selected server if available
        $this->selectedServerId = Server::first()?->id;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedServerId')
                    ->label('Select Server')
                    ->options(Server::pluck('name', 'id'))
                    ->live() // Make the field live to update charts dynamically
                    ->afterStateUpdated(function (?int $state) {
                        $this->selectedServerId = $state;
                    }),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CpuMemoryChartWidget::make(['selectedServerId' => $this->selectedServerId]),
            DiskUsageChartWidget::make(['selectedServerId' => $this->selectedServerId]),
            ServiceStatusChartWidget::make(['selectedServerId' => $this->selectedServerId]),
        ];
    }
}
