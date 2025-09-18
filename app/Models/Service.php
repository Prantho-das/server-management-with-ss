<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
