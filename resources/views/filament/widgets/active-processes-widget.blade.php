<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            Active Processes
        </x-slot>
        @livewire(\App\Livewire\ActiveProcessesTable::class, ['selectedServerId' => $selectedServerId])
    </x-filament::section>
</x-filament::widget>
