<?php

namespace App\Filament\Resources\AlertResource\Pages;

use App\Filament\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checkAlerts')
                ->label('Check Alerts Now')
                ->icon('heroicon-o-bell-alert')
                ->action(function () {
                    \Illuminate\Support\Facades\Artisan::call('alerts:check');
                    $output = \Illuminate\Support\Facades\Artisan::output();

                    \Filament\Notifications\Notification::make()
                        ->title('Alert Check Initiated')
                        ->body("Alert check initiated. Check alerts list for updates. Output: " . $output)
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => \Illuminate\Support\Facades\Auth::user()->isAdmin() || \Illuminate\Support\Facades\Auth::user()->isDevOps()),
        ];
    }
}
