<?php

namespace App\Dtos;

use App\Enums\StockMovementStatus;
use App\Enums\StockMovementType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class StockMovementCreateData extends Data
{
    public function __construct(
        #[Rule("uuid")]
        public string $stockable_id,
        public string $stockable_type,
        #[Rule("uuid")]
        public string $warehouse_id,
        public StockMovementType $type,
        public CarbonImmutable $movement_date,
        public string $reason,
        #[RequiredIf("stockable_type", "warehouse")]
        #[DataCollectionOf(StockMovementProductData::class)]
        public ?Collection $products,
        public ?CarbonImmutable $datettime,
        public StockMovementStatus $status = StockMovementStatus::LOADING,
    ) {
        $this->datettime = CarbonImmutable::now();
    }
}
