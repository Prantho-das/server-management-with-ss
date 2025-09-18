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
                Forms\Components\Textarea::make('ssh_details')
                    ->maxLength(65535),
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
