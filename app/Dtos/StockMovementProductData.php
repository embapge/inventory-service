<?php

namespace App\Dtos;

use App\Enums\StockMovementType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class StockMovementProductData extends Data
{
    public function __construct(
        #[Rule("uuid")]
        public string $id,
        #[Rule("gt:0")]
        public int $qty,
    ) {}
}
