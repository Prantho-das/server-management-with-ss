<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertResource\Pages;
use App\Filament\Resources\AlertResource\RelationManagers;
use App\Models\Alert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert'; // Changed icon

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('server_id')
                    ->relationship('server', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Select::make('severity')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                    ])
                    ->default('info')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'resolved' => 'Resolved',
                        'ignored' => 'Ignored',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\DateTimePicker::make('triggered_at')
                    ->required()
                    ->default(now()),
                Forms\Components\DateTimePicker::make('resolved_at')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('ignored_at')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('server.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('severity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('triggered_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ignored_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('server')
                    ->relationship('server', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'resolved' => 'Resolved',
                        'ignored' => 'Ignored',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Alert $record) {
                        $record->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Alert Resolved')
                            ->body("Alert #{\$record->id} has been resolved.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Alert $record): bool => $record->status === 'active'),
                Tables\Actions\Action::make('ignore')
                    ->label('Ignore')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->action(function (Alert $record) {
                        $record->update([
                            'status' => 'ignored',
                            'ignored_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Alert Ignored')
                            ->body("Alert #{\$record->id} has been ignored.")
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Alert $record): bool => $record->status === 'active'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin() || Auth::user()->isDevOps();
    }
}
