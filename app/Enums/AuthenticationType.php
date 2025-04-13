<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AuthenticationType: string
{
    use EnumToArray;

    case NONE = 'none';
    case BASIC = 'basic';
    case TOKEN = 'token';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Not set',
            self::BASIC => 'Basic Auth',
            self::TOKEN => 'Token',
        };
    }
}
