<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'service_id',
        'type',
        'message',
        'severity',
        'status',
        'triggered_at',
        'resolved_at',
        'ignored_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
        'ignored_at' => 'datetime',
    ];

    /**
     * Get the server that owns the alert.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the service that owns the alert.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
