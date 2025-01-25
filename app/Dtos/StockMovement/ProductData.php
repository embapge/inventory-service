<?php

namespace App\Dtos\StockMovement;

use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Dto;

class ProductData extends Dto
{
    public function __construct(
        public string $id,
        public int $qty,
    ) {}
}
