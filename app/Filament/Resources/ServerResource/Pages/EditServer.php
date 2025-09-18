<?php

namespace App\Filament\Resources\ServerResource\Pages;

use App\Filament\Resources\ServerResource;
use App\Filament\Widgets\ActiveProcessesWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Widgets\CpuMemoryChartWidget; // Correct Import
use App\Filament\Widgets\DiskUsageChartWidget; // Correct Import
use App\Filament\Widgets\ServiceStatusChartWidget; // Correct Import
use App\Livewire\ActiveProcessesTable; // Correct Import
use Filament\Forms\Components\TextInput; // Import TextInput
use Filament\Notifications\Notification; // Import Notification
use phpseclib3\Net\SSH2; // Import SSH2

class EditServer extends EditRecord
{
    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('runCommand')
                ->label('Run Command (SSH)')
                ->icon('heroicon-o-command-line')
                ->visible(fn (): bool => $this->record->connection_type === 'push')
                ->form([
                    TextInput::make('command')
                        ->label('Command to Run')
                        ->required()
                        ->placeholder('e.g., ls -la /var/www'),
                ])
                ->action(function (array $data) {
                    $server = $this->record;
                    $command = $data['command'];
                    $output = '';
                    $error = '';

                    if (empty($server->ssh_username)) {
                        Notification::make()
                            ->title('Error')
                            ->body('SSH username is not configured for this server.')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $ssh = new SSH2($server->ip_address, $server->ssh_port);

                        if (!empty($server->ssh_password)) {
                            if (!$ssh->login($server->ssh_username, $server->ssh_password)) {
                                throw new \Exception('SSH Password Login Failed');
                            }
                        } elseif (!empty($server->ssh_private_key)) {
                            $key = new \phpseclib3\Crypt\RSA();
                            $key->load($server->ssh_private_key);
                            if (!$ssh->login($server->ssh_username, $key)) {
                                throw new \Exception('SSH Private Key Login Failed');
                            }
                        } else {
                            throw new \Exception('No SSH credentials provided (password or private key).');
                        }

                        $output = $ssh->exec($command);
                        $error = $ssh->getStdError();

                        Notification::make()
                            ->title('Command Executed')
                            ->body("Command: `{\$command}`\nOutput: {\$output}\nError: {\$error}")
                            ->success()
                            ->persistent()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Command Execution Failed')
                            ->body("Error: " . $e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CpuMemoryChartWidget::make(['selectedServerId' => $this->record->id]),
            DiskUsageChartWidget::make(['selectedServerId' => $this->record->id]),
            ServiceStatusChartWidget::make(['selectedServerId' => $this->record->id]),
            ActiveProcessesWidget::make(['selectedServerId' => $this->record->id]),
        ];
    }
}