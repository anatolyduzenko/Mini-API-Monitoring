<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Endpoint extends Model
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
        'username',
        'password',
        'auth_type',
        'auth_token',
        'auth_token_name',
        'auth_url',
        'dashboard_visible',
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
