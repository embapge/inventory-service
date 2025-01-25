<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum StockMovementType: string
{
    case INBOUND = "inbound";
    case OUTBOUND = "outbound";

    public static function fromValue(string $value): self
    {
        foreach (self::cases() as $status) {
            if (Str::lower($value) === $status->value) {
                return $status;
            }
        }

        throw new \ValueError("$value not valid" . self::class);
    }
}
