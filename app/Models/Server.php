<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'hostname',
        'ssh_details',
        'os',
        'cpu',
        'ram',
        'disk',
        'status',
    ];

    protected $casts = [
        'ssh_details' => 'encrypted',
    ];
}
