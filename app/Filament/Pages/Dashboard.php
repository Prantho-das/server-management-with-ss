<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ServerStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\AlertListWidget::class,
            \App\Filament\Widgets\CriticalServerListWidget::class,
        ];
    }
}
