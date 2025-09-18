<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth'; // Changed icon

    // Define fixed setting keys that cannot be deleted
    protected static array $fixedSettingKeys = [
        'slack_webhook_url',
        'discord_webhook_url',
        'app_name',
        'admin_email_for_alerts',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('value')
                    ->required()
                    ->maxLength(65535)
                    // ->password() // Mask sensitive values
                    ->dehydrateStateUsing(fn (string $state): string => $state) // Don't re-encrypt if already encrypted
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->visible(fn (string $operation): bool => $operation === 'create' || (Auth::user()->isAdmin())),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value (Masked)')
                    ->getStateUsing(fn (Setting $record): string => '********') // Always mask in table
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
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Setting $record): bool => !static::isFixedSetting($record->key)), // Disable delete for fixed settings
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make()
            //             ->visible(fn (Tables\Actions\DeleteBulkAction $action): bool => $action->getRecords()->every(fn (Setting $record): bool => !static::isFixedSetting($record->key))),
            //     ]),
            // ])
            ;
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin(); // Only admin can access settings
    }

    // Helper method to check if a setting key is fixed
    protected static function isFixedSetting(string $key): bool
    {
        return in_array($key, static::$fixedSettingKeys);
    }
}
