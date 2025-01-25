<?php

namespace App\Resources;

use App\Enums\ComparisonDetailStatus;
use App\Models\ComparisonDetail;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

class ComparisonDetailResource extends Resource
{
    public function __construct(
        public string $id,
        public Lazy|ComparisonResource|null $comparison,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|PurchaseOrderResource|null $purchaseOrder,
        public ComparisonDetailStatus $status
    ) {}

    public static function fromModel(ComparisonDetail $detail): self
    {
        return new self(
            $detail->id,
            Lazy::whenLoaded("comparison", $detail, fn() => ComparisonResource::from($detail->comparison)),
            Lazy::whenLoaded("purchaseOrder", $detail, fn() => PurchaseOrderResource::from($detail->purchaseOrder)),
            $detail->status
        );
    }
}
