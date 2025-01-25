<?php

namespace App\Dtos\StockMovement;

use App\Dtos\StockMovement\ProductData as StockMovementProductData;
use App\Enums\StockMovementType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Dto;
use Spatie\LaravelData\Lazy;

class CreateData extends Dto
{
    public function __construct(
        public string $stockable_id,
        public string $stockable_type,
        public string $warehouse_id,
        public StockMovementType $type,
        public CarbonImmutable $movement_date,
        public string $reason,
        #[RequiredIf("stockable", "warehouse")]
        #[DataCollectionOf(StockMovementProductData::class)]
        public ?Collection $products,
    ) {}
}
