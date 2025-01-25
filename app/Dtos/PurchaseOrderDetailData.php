<?php

namespace App\Dtos;

use App\Models\PurchaseOrderDetail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

class PurchaseOrderDetailData extends Data
{
    public function __construct(
        public ?string $id,
        #[FromRouteParameter('purchaseOrderId')]
        #[Rule("uuid")]
        public ?string $purchase_order_id,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|PurchaseOrderData|null $purchaseOrder,
        // public Lazy|ProductData $product,
        public PurchaseOrderDetailProductData $product,
        #[Rule("gt:0")]
        public int $qty,
        public ?string $created_by,
        public ?string $updated_by,
        public ?CarbonImmutable $created_at,
        public ?CarbonImmutable $updated_at,
        public float $total = 0,
    ) {}

    public static function fromModel(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        return new self(
            $purchaseOrderDetail->id,
            $purchaseOrderDetail->purchase_order_id,
            Lazy::whenLoaded("purchaseOrder", $purchaseOrderDetail, fn() => PurchaseOrderData::from($purchaseOrderDetail->purchaseOrder())),
            // Lazy::whenLoaded("product", $purchaseOrderDetail, fn() => ProductData::from($purchaseOrderDetail->product->id)) ?? $purchaseOrderDetail->product,
            $purchaseOrderDetail->product,
            $purchaseOrderDetail->qty,
            $purchaseOrderDetail->created_by,
            $purchaseOrderDetail->updated_by,
            $purchaseOrderDetail->created_at,
            $purchaseOrderDetail->updated_at,
            $purchaseOrderDetail->total,
        );
    }
}
