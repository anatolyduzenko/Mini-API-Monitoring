<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AuthenticationType: string
{
    use EnumToArray;
    case BASIC = 'basic';
    case TOKEN = 'token';

    public function label(): string
    {
        return match ($this) {
            self::BASIC => 'Basic Auth',
            self::TOKEN => 'Token',
        };
    }
}
