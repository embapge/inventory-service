<?php

namespace App\Resources;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

class StockMovementResource extends Resource
{
    public function __construct(
        public string $id,
        public string $stockable_id,
        public string $stockable_type,
        public $stockable,
        public string $warehouse_id,
        public Lazy|WarehouseResource|null $warehouse,
        public StockMovementType $type,
        public CarbonImmutable $movement_date,
        #[Rule(["required_array_keys:name,datetime,reason"])]
        #[DataCollectionOf(StockMovementStatusResource::class)]
        public Collection $status,
        #[DataCollectionOf(StockMovementDetailResource::class)]
        public Lazy|Collection|null $details,
    ) {}

    public static function fromModel(StockMovement $movement): self
    {
        return new self(
            $movement->id,
            $movement->stockable_id,
            $movement->stockable_type,
            $movement->stockable_type == "warehouse" ? WarehouseResource::from($movement->stockable) : PurchaseOrderResource::from($movement->stockable),
            $movement->warehouse_id,
            Lazy::whenLoaded("warehouse", $movement, fn() => WarehouseResource::from($movement->warehouse)),
            $movement->type,
            $movement->movement_date,
            $movement->status->map(fn($status) => StockMovementStatusResource::from($status)),
            Lazy::whenLoaded("details", $movement, fn() => StockMovementDetailResource::collect($movement->details))
        );
    }
}
