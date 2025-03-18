<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    protected $fillable = [
        'name',
        'url',
        'method',
        'headers',
        'body',
        'check_interval'
    ];

    public function logs() {
        return $this->hasMany(EndpointLog::class);
    }
    
}
