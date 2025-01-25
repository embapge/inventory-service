<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ComparisonDetailStatus: string
{
    case PENDING = "pending";
    case APPROVED = "approved";
    case DECLINED = "declined";

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
