<?php

namespace App\Resources;

use Spatie\LaravelData\Resource;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class StockMovementStatusResource extends Resource
{
    public function __construct(
        public string $name,
        public string $datetime,
        public string $reason,
    ) {}
}
