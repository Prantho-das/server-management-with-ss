<?php

namespace App\Filament\Widgets;

use App\Models\Alert;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AlertListWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(Alert::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('message'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ]);
    }
}
