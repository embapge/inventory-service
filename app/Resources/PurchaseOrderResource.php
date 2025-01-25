<?php

namespace App\Resources;

use App\Resources\PurchaseOrderDetailResource;
use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Lazy;

class PurchaseOrderResource extends Resource
{
    public function __construct(
        public string $id,
        public SupplierResource $supplier,
        public CarbonImmutable $order_date,
        public float $total_amount,
        public PurchaseOrderStatus $status,
        public Lazy|Collection|null $details
    ) {}

    public static function fromModel(PurchaseOrder $purchaseOrder): self
    {
        return new self(
            $purchaseOrder->id,
            SupplierResource::from($purchaseOrder->supplier),
            $purchaseOrder->order_date,
            $purchaseOrder->total_amount,
            $purchaseOrder->status,
            Lazy::whenLoaded("details", $purchaseOrder, fn() => PurchaseOrderDetailResource::collect($purchaseOrder->details))
        );
    }
}
