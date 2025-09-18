<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'pid',
        'user',
        'command',
        'cpu_percent',
        'memory_percent',
        'status',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    /**
     * Get the server that owns the process.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
