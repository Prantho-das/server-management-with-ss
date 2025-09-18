<?php

namespace App\Filament\Resources\MetricResource\Pages;

use App\Filament\Resources\MetricResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Server; // Import Server model

class ListMetrics extends ListRecords
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('collectMetrics')
                ->label('Collect Metrics')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Select::make('server_id')
                        ->label('Server')
                        ->options(Server::where('connection_type', 'push')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $server = Server::find($data['server_id']);
                    if (!$server) {
                        Notification::make()
                            ->title('Error')
                            ->body('Server not found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    \Illuminate\Support\Facades\Artisan::call('metrics:collect', ['server_id' => $server->id]);
                    $output = \Illuminate\Support\Facades\Artisan::output();

                    Notification::make()
                        ->title('Metric Collection Initiated')
                        ->body("Metric collection for server " . $server->name . " initiated. Check metrics list for updates. Output: " . $output)
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => \Illuminate\Support\Facades\Auth::user()->isAdmin() || \Illuminate\Support\Facades\Auth::user()->isDevOps()),
        ];
    }
}
