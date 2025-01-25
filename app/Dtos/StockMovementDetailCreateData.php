<?php

namespace App\Dtos;

use App\Enums\StockMovementStatus;
use App\Enums\StockMovementType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\RequiredArrayKeys;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class StockMovementDetailCreateData extends Data
{
    public function __construct(
        #[Rule("uuid")]
        public string $stock_movement_id,
        #[Rule("uuid")]
        public string $product_id,
        #[Rule("gt:0")]
        public int $qty,
        #[RequiredArrayKeys("name", "datetime", "reason")]
        public ?array $status
    ) {}
}
