<?php

namespace App\Filament\Resources\MetricResource\Pages;

use App\Filament\Resources\MetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMetric extends EditRecord
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
