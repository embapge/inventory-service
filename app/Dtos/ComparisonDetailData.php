<?php

namespace App\Dtos;

use App\Enums\ComparisonDetailStatus;
use App\Models\ComparisonDetail;
use DateTime;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

class ComparisonDetailData extends Data
{
    public function __construct(
        #[FromRouteParameter('comparisonDetailId')]
        #[Rule("uuid")]
        public ?string $id,
        #[Rule("uuid")]
        public string $purchase_order_id,
        #[MapInputName(SnakeCaseMapper::class)]
        public Lazy|PurchaseOrderData|null $purchaseOrder,
        #[RequiredWithout("id")]
        public ?string $comparison_id,
        public Lazy|ComparisonData|null $comparison,
        public ComparisonDetailStatus $status = ComparisonDetailStatus::PENDING
    ) {}

    public static function fromModel(ComparisonDetail $comparisonDetail): self
    {
        return new self(
            $comparisonDetail->id,
            $comparisonDetail->purchase_order_id,
            Lazy::whenLoaded("purchaseOrder", $comparisonDetail, fn() => PurchaseOrderData::from($comparisonDetail->purchaseOrder)),
            $comparisonDetail->comparison_id,
            Lazy::whenLoaded("comparison", $comparisonDetail, fn() => ComparisonData::from($comparisonDetail->comparison)),
            $comparisonDetail->status,
        );
    }
}
