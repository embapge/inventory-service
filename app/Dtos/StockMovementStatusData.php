<?php

namespace App\Dtos;

use App\Enums\StockMovementStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class StockMovementStatusData extends Data
{
    public function __construct(
        public CarbonImmutable $datetime,
        public StockMovementStatus $name = StockMovementStatus::LOADING,
        public ?string $reason,
    ) {
        $this->datetime = CarbonImmutable::now();
    }
}
