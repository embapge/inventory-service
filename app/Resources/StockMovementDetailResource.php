<?php

namespace App\Resources;

use App\Models\StockMovementDetail;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

class StockMovementDetailResource extends Resource
{
    public function __construct(
        public string $id,
        public string $stock_movement_id,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|StockMovementResource|null $stockMovement,
        public string $product_id,
        public Lazy|ProductResource|null $product,
        public int $qty
    ) {}

    public static function fromModel(StockMovementDetail $movementDetail): self
    {
        return new self(
            $movementDetail->id,
            $movementDetail->stock_movement_id,
            Lazy::whenLoaded("stockMovement", $movementDetail, fn() => StockMovementResource::from($movementDetail->movement)),
            $movementDetail->product_id,
            Lazy::whenLoaded("product", $movementDetail, fn() => ProductResource::from($movementDetail->product)),
            $movementDetail->qty,
        );
    }
}
