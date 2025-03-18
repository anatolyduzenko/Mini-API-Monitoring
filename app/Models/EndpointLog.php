<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndpointLog extends Model
{
    protected $fillable = [
        'endpoint_id',
        'status_code',
        'response_time'
    ];
}
