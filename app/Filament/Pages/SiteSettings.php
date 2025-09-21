<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = Setting::all()->pluck('value', 'key')->toArray();
        // Transform app_logo for FileUpload component
        if (isset($this->data['app_logo']) && $this->data['app_logo']) {
            $this->data['app_logo'] = [$this->data['app_logo']];
        }
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('app_name')->label('Application Name'),
                TextInput::make('admin_email_for_alerts')->label('Admin Email for Alerts'),
                FileUpload::make('app_logo')
                    ->label('Application Logo')
                    ->image()
                    ->disk('public')
                    ->directory('logos') // Store in a 'logos' subdirectory
                    ->dehydrateStateUsing(function ($state) {
                        if (is_array($state) && isset($state[0])) {
                            return $state[0]; // Return the path to the uploaded file
                        }
                        return $state; // Return null or existing path
                    }),
                Select::make('app_theme')
                    ->label('Theme')
                    ->options([
                        'default' => 'Default',
                        'red' => 'Red',
                        'green' => 'Green',
                        'blue' => 'Blue',
                    ]),
                ColorPicker::make('app_primary_color')->label('Primary Color'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        Cache::forget('app_settings');

        $this->dispatch('setting-updated');
    }
}