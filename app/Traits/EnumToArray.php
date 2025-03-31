<?php

namespace App\Traits;

trait EnumToArray
{
    public static function fromCode(int $code): ?self
    {
        return self::tryFrom($code);
    }

    public static function asLabels(): array
    {
        return array_map(fn ($case) => [
            'id' => $case->value,
            'name' => $case->label(),
        ], self::cases());
    }
}
