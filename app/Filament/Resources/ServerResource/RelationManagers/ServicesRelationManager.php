<?php

namespace App\Filament\Resources\ServerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('process_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('port')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('version')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'running' => 'Running',
                        'stopped' => 'Stopped',
                        'unknown' => 'Unknown',
                    ])
                    ->default('unknown')
                    ->required(),
                Forms\Components\TextInput::make('cpu_usage')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('memory_usage')
                    ->numeric()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('process_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpu_usage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('memory_usage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated') // Changed label
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Made visible by default
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'running' => 'Running',
                        'stopped' => 'Stopped',
                        'unknown' => 'Unknown',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
