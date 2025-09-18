<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this line

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'name',
        'process_name',
        'port',
        'version',
        'status',
        'cpu_usage',
        'memory_usage',
    ];

    /**
     * Get the server that owns the service.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the alerts for the service.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
