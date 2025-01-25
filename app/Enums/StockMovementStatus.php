<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum StockMovementStatus: string
{
    case LOADING = "loading";
    case APPROVED = "approved";
    case DECLINED = "decline";
    case PROCESS = "process";
    case DONE = "done";
    case CANCEL = "cancel";

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
