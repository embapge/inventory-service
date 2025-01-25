<?php

namespace App\Resources;

use App\Enums\WarehouseType;
use App\Models\Warehouse;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;

class WarehouseResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public WarehouseType $type,
        public string $location,
        public string $lat,
        public string $lang,
        public int $capacity,
        public Lazy|Collection|null $shelfs
    ) {
        //
    }

    public static function fromModel(Warehouse $warehouse): self
    {
        return new self(
            $warehouse->id,
            $warehouse->name,
            $warehouse->type,
            $warehouse->location,
            $warehouse->lat,
            $warehouse->lang,
            $warehouse->capacity,
            Lazy::whenLoaded("shelfs", $warehouse, fn() => ShelfResource::collect($warehouse->shelfs))
        );
    }
}
