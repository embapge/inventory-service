<?php

namespace App\Dtos;

use App\Dtos\CategoryData;
use App\Dtos\WarehouseData;
use App\Models\Category;
use App\Models\Shelf;
use App\Models\Warehouse;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class ShelfData extends Data
{
    public function __construct(
        #[Rule("uuid")]
        #[FromRouteParameter("shelfId")]
        public ?string $id,
        #[Rule("uuid")]
        public string $warehouse_id,
        public Lazy|WarehouseData|null $warehouse,
        public string $category_id,
        public Lazy|CategoryData|null $category,
        public ?string $code,
        public int $capacity
    ) {}

    public static function fromModel(Shelf $shelf): self
    {
        return new self(
            $shelf->id,
            $shelf->warehouse_id,
            Lazy::whenLoaded("warehouse", $shelf, fn() => WarehouseData::from($shelf->warehouse)),
            $shelf->category_id,
            Lazy::whenLoaded("category", $shelf, fn() => CategoryData::from($shelf->category)),
            $shelf->code,
            $shelf->capacity
        );
    }
}
