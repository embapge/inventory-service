<?php

namespace App\Dtos;

use App\DTOs\ShelfData;
use App\Enums\WarehouseType;
use App\Models\Warehouse;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class WarehouseData extends Data
{
    public function __construct(
        #[FromRouteParameter('warehouseId')]
        #[Rule("uuid")]
        public ?string $id,
        public Lazy|Collection|null $shelfs,
        public string $name,
        public WarehouseType $type,
        public string $location,
        public string $lat,
        public string $lang,
        public int $capacity,
    ) {}

    public static function fromModel(Warehouse $warehouse): self
    {
        return new self(
            $warehouse->id,
            Lazy::whenLoaded("shelfs", $warehouse, fn() => ShelfData::collect($warehouse->shelfs)),
            $warehouse->name,
            $warehouse->type,
            $warehouse->location,
            $warehouse->lat,
            $warehouse->lang,
            $warehouse->capacity,
        );
    }
}
