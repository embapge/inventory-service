<?php

namespace App\Dtos;

use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class PurchaseOrderDetailProductData extends Data
{
    public function __construct(
        #[Rule("uuid")]
        public ?string $id,
        #[Rule("uuid")]
        public ?string $category_id,
        public ?string $name,
        public ?float $price,
        public ?string $description,
        #[Rule("required_array_keys:id,name,description")]
        public array|Collection|null $category
    ) {}
}
