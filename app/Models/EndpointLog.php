<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class EndpointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'status_code',
        'response_time',
    ];

    public function endpoint()
    {
        return $this->belongsTo(Endpoint::class);
    }
}
