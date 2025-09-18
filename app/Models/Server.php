<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Add this line
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this line

class Server extends Model
{
    use HasFactory, HasApiTokens; // Add HasApiTokens here

    protected $fillable = [
        'name',
        'ip_address',
        'hostname',
        'connection_type',
        'ssh_username', // New
        'ssh_password', // New
        'ssh_private_key', // New
        'ssh_port', // New
        'os',
        'cpu',
        'ram',
        'disk',
        'status',
    ];

    protected $casts = [
        'ssh_password' => 'encrypted', // New
        'ssh_private_key' => 'encrypted', // New
    ];

    /**
     * Get the services for the server.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the processes for the server.
     */
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    /**
     * Get the metrics for the server.
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class);
    }

    /**
     * Get the alerts for the server.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
