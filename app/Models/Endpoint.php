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
        'check_interval',
        'user_id'
    ];

    public function logs() {
        return $this->hasMany(EndpointLog::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
