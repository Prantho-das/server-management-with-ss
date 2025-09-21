<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServerResource\Pages;
use App\Filament\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Filament\Widgets\CpuMemoryChartWidget; // Import
use App\Filament\Widgets\DiskUsageChartWidget; // Import
use App\Filament\Widgets\ServiceStatusChartWidget; // Import
use App\Livewire\ActiveProcessesTable; // Import

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static ?string $navigationIcon = 'heroicon-o-server'; // Changed icon to server

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ip_address')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('hostname')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('connection_type')
                    ->options([
                        'pull' => 'Pull System (API Report)',
                        'push' => 'Push System (SSH)',
                    ])
                    ->default('pull')
                    ->required()
                    ->live(), // Make it live to react to changes
                Forms\Components\Select::make('os_type')
                    ->options([
                        'ubuntu' => 'Ubuntu/Debian',
                        'centos' => 'CentOS/RHEL',
                    ])
                    ->default('ubuntu')
                    ->required(),
                Forms\Components\Group::make()
                    ->visible(fn (Forms\Get $get): bool => $get('connection_type') === 'push')
                    ->schema([
                        Forms\Components\TextInput::make('ssh_username')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('ssh_port')
                            ->numeric()
                            ->default(22)
                            ->required(),
                        Forms\Components\Select::make('authentication_type')
                            ->options([
                                'password' => 'Password',
                                'key' => 'Private Key',
                            ])
                            ->default('password')
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('ssh_password')
                            ->password()
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('authentication_type') === 'password'),
                        Forms\Components\Textarea::make('ssh_private_key')
                            ->maxLength(65535)
                            ->visible(fn (Forms\Get $get): bool => $get('authentication_type') === 'key'),
                    ]),
                Forms\Components\TextInput::make('os')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cpu')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ram')
                    ->maxLength(255),
                Forms\Components\TextInput::make('disk')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                        'unknown' => 'Unknown',
                    ])
                    ->default('unknown')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hostname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('os')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ram')
                    ->searchable(),
                Tables\Columns\TextColumn::make('disk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('testConnection')
                    ->label('Test Connection')
                    ->icon('heroicon-o-signal')
                    ->action(function (Server $record) {
                        \Illuminate\Support\Facades\Artisan::call('server:test', ['id' => $record->id]);
                        $output = \Illuminate\Support\Facades\Artisan::output();

                        if ($record->status === 'online') {
                            \Filament\Notifications\Notification::make()
                                ->title('Connection Test Successful')
                                ->body("Server " . $record->name . " is online.")
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Connection Test Failed')
                                ->body("Server " . $record->name . " is offline or unreachable. Output: " . $output)
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Server $record): bool => Auth::user()->isAdmin() || Auth::user()->isDevOps()),
                Tables\Actions\Action::make('scanServices')
                    ->label('Scan Services')
                    ->icon('heroicon-o-magnifying-glass')
                    ->action(function (Server $record) {
                        \Illuminate\Support\Facades\Artisan::call('service:scan', ['server_id' => $record->id]);
                        $output = \Illuminate\Support\Facades\Artisan::output();

                        \Filament\Notifications\Notification::make()
                            ->title('Service Scan Initiated')
                            ->body("Service scan for server " . $record->name . " initiated. Check services list for updates. Output: " . $output)
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Server $record): bool => ($record->connection_type === 'push') && (Auth::user()->isAdmin() || Auth::user()->isDevOps())),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin() || Auth::user()->isDevOps();
    }
}
