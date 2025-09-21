<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ActiveProcessesWidget extends Widget
{
    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.active-processes-widget';

    public ?int $selectedServerId = null; // Property to accept server ID

    protected function getViewData(): array
    {
        return [
            'selectedServerId' => $this->selectedServerId,
        ];
    }
}
