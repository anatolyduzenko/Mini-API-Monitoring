<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum RequestType: string
{
    use EnumToArray;
    case GET = 'GET';
    case FETCH = 'FETCH';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';

    public function label(): string
    {
        return match ($this) {
            self::GET => 'GET',
            self::FETCH => 'FETCH',
            self::HEAD => 'HEAD',
            self::POST => 'POST',
            self::PUT => 'PUT',
        };
    }
}
