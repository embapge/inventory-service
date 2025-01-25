<?php

namespace App\Dtos;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class PurchaseOrderData extends Data
{
    public function __construct(
        public ?string $id,
        #[Rule("required_without:id", "uuid")]
        public string $supplier_id,
        public Lazy|SupplierData|null $supplier,
        public CarbonImmutable $order_date,
        public ?string $created_by,
        public ?string $updated_by,
        public ?CarbonImmutable $created_at,
        public ?CarbonImmutable $updated_at,
        #[DataCollectionOf(PurchaseOrderDetailData::class)]
        public Lazy|Collection|null $details,
        public float $total_amount = 0.00,
        public PurchaseOrderStatus $status = PurchaseOrderStatus::PENDING,
    ) {}

    public static function fromModel(PurchaseOrder $purchaseOrder): self
    {
        return new self(
            $purchaseOrder->id,
            $purchaseOrder->supplier_id,
            Lazy::whenLoaded("supplier", $purchaseOrder, fn() => SupplierData::from($purchaseOrder->supplier)),
            $purchaseOrder->order_date,
            $purchaseOrder->created_by,
            $purchaseOrder->updated_by,
            $purchaseOrder->created_at,
            $purchaseOrder->updated_at,
            Lazy::whenLoaded("details", $purchaseOrder, fn() => PurchaseOrderDetailData::collect($purchaseOrder->details)),
            $purchaseOrder->total_amount,
            $purchaseOrder->status,
        );
    }
}
