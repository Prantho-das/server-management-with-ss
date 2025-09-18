<x-filament-panels::page>
    <div class="fi-page-content">
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            Monitoring Dashboard
        </h1>

        <div class="mb-4">
            {{ $this->form }}
        </div>

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-filament::section class="col-span-1">
                <x-slot name="heading">Total Servers</x-slot>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Server::count() }}</p>
            </x-filament::section>

            <x-filament::section class="col-span-1">
                <x-slot name="heading">Active Servers</x-slot>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Server::where('status', 'online')->count() }}</p>
            </x-filament::section>

            <x-filament::section class="col-span-1">
                <x-slot name="heading">Critical Servers</x-slot>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Server::whereHas('alerts', fn ($query) => $query->where('severity', 'critical')->where('status', 'active'))->count() }}</p>
            </x-filament::section>

            <x-filament::section class="col-span-1">
                <x-slot name="heading">Total Services</x-slot>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Service::count() }}</p>
            </x-filament::section>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 mb-6">
            <x-filament::section class="col-span-1">
                @livewire(\App\Filament\Widgets\CpuMemoryChartWidget::class, ['selectedServerId' => $this->selectedServerId])
            </x-filament::section>

            <x-filament::section class="col-span-1">
                @livewire(\App\Filament\Widgets\DiskUsageChartWidget::class, ['selectedServerId' => $this->selectedServerId])
            </x-filament::section>

            <x-filament::section class="col-span-full">
                @livewire(\App\Filament\Widgets\ServiceStatusChartWidget::class, ['selectedServerId' => $this->selectedServerId])
            </x-filament::section>
        </div>

        {{-- Active Processes Table --}}
        <x-filament::section>
            <x-slot name="heading">
                Active Processes
            </x-slot>
            @livewire(\App\Livewire\ActiveProcessesTable::class, ['selectedServerId' => $this->selectedServerId])
        </x-filament::section>
    </div>
</x-filament-panels::page>
