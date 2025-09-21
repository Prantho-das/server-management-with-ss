<?php

namespace App\Filament\Resources\ServerResource\Pages;

use App\Filament\Resources\ServerResource;
use App\Models\ApplicationTemplate;
use App\Filament\Widgets\ActiveProcessesWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Widgets\CpuMemoryChartWidget; // Correct Import
use App\Filament\Widgets\DiskUsageChartWidget; // Correct Import
use App\Filament\Widgets\ServiceStatusChartWidget; // Correct Import
use App\Livewire\ActiveProcessesTable; // Correct Import
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification; // Import Notification
use phpseclib3\Net\SSH2; // Import SSH2
use phpseclib3\Crypt\RSA;

class EditServer extends EditRecord
{
    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            // Run Command (SSH)
            Actions\Action::make('runCommand')
                ->label('Run Command (SSH)')
                ->icon('heroicon-o-command-line')
                ->visible(fn(): bool => $this->record->connection_type === 'push')
                ->form([
                    TextInput::make('command')
                        ->label('Command to Run')
                        ->required()
                        ->placeholder('e.g., ls -la /var/www'),
                ])
                ->action(function (array $data) {
                    $server = $this->record;
                    $command = $data['command'];

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
                            $key = RSA::loadPrivateKey($server->ssh_private_key);
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
                            ->body("Command: `{$command}`\nOutput: {$output}\nError: {$error}")
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

            // Manage Packages
            Actions\Action::make('managePackages')
                ->label('Manage Packages')
                ->icon('heroicon-o-beaker')
                ->visible(fn(): bool => $this->record->connection_type === 'push')
                ->form([
                    Select::make('action')
                        ->options([
                            'install' => 'Install',
                            'restart' => 'Restart',
                        ])
                        ->required()
                        ->live(),
                    TextInput::make('package')
                        ->label('Package Name')
                        ->required()
                        ->placeholder('e.g., nginx'),
                    TextInput::make('version')
                        ->label('Version')
                        ->placeholder('e.g., 1.20.1')
                        ->visible(fn($get) => $get('action') === 'install'),
                ])
                ->action(function (array $data) {
                    $server = $this->record;
                    $action = $data['action'];
                    $package = $data['package'];
                    $version = $data['version'];
                    $command = '';

                    if ($action === 'install') {
                        if ($server->os_type === 'ubuntu') {
                            $command = "apt-get update && apt-get install -y {$package}";
                            if ($version) {
                                $command .= "={$version}";
                            }
                        } elseif ($server->os_type === 'centos') {
                            $command = "yum install -y {$package}";
                            if ($version) {
                                $command .= "-{$version}";
                            }
                        }
                    } elseif ($action === 'restart') {
                        $command = "systemctl restart {$package}";
                    }

                    if (empty($command)) {
                        Notification::make()
                            ->title('Error')
                            ->body('Could not determine the command to run for the selected OS type.')
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
                            $key = RSA::loadPrivateKey($server->ssh_private_key);
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
                            ->body("Command: `{$command}`\nOutput: {$output}\nError: {$error}")
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

            // List Installed Packages
            Actions\Action::make('listPackages')
                ->label('List Installed Packages')
                ->icon('heroicon-o-archive-box')
                ->visible(fn(): bool => $this->record->connection_type === 'push')
                ->action(function () {
                    $server = $this->record;
                    $command = '';

                    if ($server->os_type === 'ubuntu') {
                        $command = 'apt list --installed';
                    } elseif ($server->os_type === 'centos') {
                        $command = 'yum list installed';
                    }

                    if (empty($command)) {
                        Notification::make()
                            ->title('Error')
                            ->body('Could not determine the command to run for the selected OS type.')
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
                            $key = RSA::loadPrivateKey($server->ssh_private_key);
                            if (!$ssh->login($server->ssh_username, $key)) {
                                throw new \Exception('SSH Private Key Login Failed');
                            }
                        } else {
                            throw new \Exception('No SSH credentials provided (password or private key).');
                        }

                        $output = $ssh->exec($command);

                        Notification::make()
                            ->title('Installed Packages')
                            ->body("<pre>{$output}</pre>")
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

            // Deploy Application Template
            Actions\Action::make('deployTemplate')
                ->label('Deploy Application Template')
                ->icon('heroicon-o-rocket-launch')
                ->visible(fn(): bool => $this->record->connection_type === 'push')
                ->form([
                    Select::make('application_template_id')
                        ->label('Application Template')
                        ->options(ApplicationTemplate::pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $server = $this->record;
                    $template = ApplicationTemplate::find($data['application_template_id']);

                    if (!$template) {
                        Notification::make()
                            ->title('Error')
                            ->body('Application template not found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $script = $template->script;

                    try {
                        $ssh = new SSH2($server->ip_address, $server->ssh_port);

                        if (!empty($server->ssh_password)) {
                            if (!$ssh->login($server->ssh_username, $server->ssh_password)) {
                                throw new \Exception('SSH Password Login Failed');
                            }
                        } elseif (!empty($server->ssh_private_key)) {
                            $key = RSA::loadPrivateKey($server->ssh_private_key);
                            if (!$ssh->login($server->ssh_username, $key)) {
                                throw new \Exception('SSH Private Key Login Failed');
                            }
                        } else {
                            throw new \Exception('No SSH credentials provided (password or private key).');
                        }

                        $output = $ssh->exec($script);
                        $error = $ssh->getStdError();

                        Notification::make()
                            ->title('Deployment Script Executed')
                            ->body("Script: `{$script}`\nOutput: {$output}\nError: {$error}")
                            ->success()
                            ->persistent()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Deployment Failed')
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
