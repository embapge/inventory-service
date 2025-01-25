<?php

namespace App\Resources;

use App\Models\PurchaseOrderDetail;
use App\Resources\PurchaseOrderResource;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

class PurchaseOrderDetailResource extends Data
{
    public function __construct(
        public string $id,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|PurchaseOrderResource|null $purchaseOrder,
        public Collection $product,
        public int $qty,
        public float $total,
    ) {}

    public static function fromModel(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        return new self(
            $purchaseOrderDetail->id,
            Lazy::whenLoaded("purchaseOrder", $purchaseOrderDetail, fn() => PurchaseOrderResource::from($purchaseOrderDetail->purchaseOrder)),
            $purchaseOrderDetail->product,
            $purchaseOrderDetail->qty,
            $purchaseOrderDetail->total,
        );
    }
}
