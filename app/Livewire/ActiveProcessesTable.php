<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Process;
use App\Models\Server;
use Livewire\WithPagination; // For pagination if needed, though not explicitly requested

class ActiveProcessesTable extends Component
{
    use WithPagination;

    public ?int $selectedServerId = null;
    public ?string $statusFilter = null;
    public ?string $processNameFilter = null;

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'processNameFilter' => ['except' => ''],
    ];

    public function mount(?int $selectedServerId = null)
    {
        $this->selectedServerId = $selectedServerId;
    }

    public function render()
    {
        $processes = Process::query()
            ->when($this->selectedServerId, function ($query) {
                $query->where('server_id', $this->selectedServerId);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->processNameFilter, function ($query) {
                $query->where('command', 'like', '%' . $this->processNameFilter . '%');
            })
            ->orderBy('cpu_percent', 'desc')
            ->paginate(10); // Paginate for better performance

        $servers = Server::pluck('name', 'id'); // For server filter dropdown

        return view('livewire.active-processes-table', [
            'processes' => $processes,
            'servers' => $servers,
        ]);
    }

    public function updatedSelectedServerId()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedProcessNameFilter()
    {
        $this->resetPage();
    }
}
