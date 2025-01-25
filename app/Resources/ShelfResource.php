<?php

namespace App\Resources;

use App\Models\Category;
use App\Models\Shelf;
use App\Models\Warehouse;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;

class ShelfResource extends Resource
{
    public function __construct(
        public string $id,
        public string $category_id,
        public Lazy|WarehouseResource|null $warehouse,
        public Lazy|CategoryResource|null $category,
        public string $code,
        public int $capacity,
    ) {}

    public static function fromModel(Shelf $shelf): self
    {
        return new self(
            $shelf->id,
            $shelf->category_id,
            Lazy::whenLoaded("warehouse", $shelf, fn() => WarehouseResource::from($shelf->warehouse)),
            Lazy::whenLoaded("category", $shelf, fn() => CategoryResource::from($shelf->category)),
            $shelf->code,
            $shelf->capacity,
        );
    }
}
