<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'method',
        'headers',
        'body',
        'check_interval',
        'user_id',
        'alert_threshold',
    ];

    public function logs()
    {
        return $this->hasMany(EndpointLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
