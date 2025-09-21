<?php

namespace App\Filament\Widgets;

use App\Models\Server;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CriticalServerListWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Server::whereHas('metrics', function ($query) {
                    $query->where('cpu_usage', '>', 80)->orWhere('memory_usage', '>', 80);
                })->orWhere('status', '!=', 'active')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status'),
            ]);
    }
}
