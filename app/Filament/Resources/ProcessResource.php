<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessResource\Pages;
use App\Filament\Resources\ProcessResource\RelationManagers;
use App\Models\Process;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Filament\Tables\Filters\TextInputFilter; // Import TextInputFilter

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip'; // Changed icon

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('server_id')
                    ->relationship('server', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('pid')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('user')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('command')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('cpu_percent')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('memory_percent')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'running' => 'Running',
                        'sleeping' => 'Sleeping',
                        'zombie' => 'Zombie',
                        'stopped' => 'Stopped',
                        'unknown' => 'Unknown',
                    ])
                    ->default('unknown')
                    ->required(),
                Forms\Components\DateTimePicker::make('started_at')
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
                Tables\Columns\TextColumn::make('pid')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('command')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('cpu_percent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('memory_percent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'running' => 'Running',
                        'sleeping' => 'Sleeping',
                        'zombie' => 'Zombie',
                        'stopped' => 'Stopped',
                        'unknown' => 'Unknown',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProcesses::route('/'),
            'create' => Pages\CreateProcess::route('/create'),
            'edit' => Pages\EditProcess::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin() || Auth::user()->isDevOps();
    }
}
