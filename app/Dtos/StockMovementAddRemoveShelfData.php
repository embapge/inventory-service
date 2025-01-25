<?php

namespace App\Dtos;

use App\Models\Product;
use App\Models\Shelf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class StockMovementAddRemoveShelfData extends Data
{
  public function __construct(
    #[Rule("uuid")]
    public string $shelf_id,
    public ?Shelf $shelf,
    #[Rule("uuid")]
    public string $product_id,
    public ?Product $product,
    #[Rule("gt:0")]
    public int $qty,
  ) {}
}
