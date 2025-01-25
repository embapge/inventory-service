<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum WarehouseType: string
{
    case STORE = "store";
    case WAREHOUSE = "warehouse";

    public static function fromValue(string $value): self
    {
        foreach (self::cases() as $status) {
            if (Str::lower($value) === $status->value) {
                return $status;
            }
        }   

        throw new \ValueError("$value bukan status yang valid" . self::class);
    }
}
