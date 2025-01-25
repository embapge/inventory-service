<?php

namespace App\Resources;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

class SupplierResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public array|Collection $phone,
        public array|Collection $email,
        public string $address,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|Collection|null $purchaseOrders
    ) {}

    public static function fromModel(Supplier $supplier): self
    {
        return new self(
            $supplier->id,
            $supplier->name,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            Lazy::whenLoaded('purchaseOrders', $supplier, fn() => PurchaseOrderResource::collect($supplier->purchaseOrders)),
        );
    }
}
