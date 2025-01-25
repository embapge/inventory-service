<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum PurchaseOrderStatus: string
{
    case PENDING = "pending";
    case PROGRESS = "progress";
    case APPROVED = "approved";
    case PAID = "paid";
    case DONE = "done";
    case REJECTED = "rejected";

    public static function fromValue(string $value): self
    {
        foreach (self::cases() as $status) {
            if (Str::lower($value) === $status->value) {
                return $status;
            }
        }

        throw new \ValueError(Str::title($value) . " bukan status yang valid" . self::class);
    }
}
