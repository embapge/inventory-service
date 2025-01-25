<?php

namespace App\Dtos;

use App\Enums\ComparisonStatus;
use App\Models\Comparison;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class ComparisonData extends Data
{
    public function __construct(
        #[FromRouteParameter('comparisonId')]
        #[Rule("uuid")]
        public ?string $id,
        public ?string $number_display,
        public CarbonImmutable $order_date,
        public ?string $created_by,
        public ?string $updated_by,
        public ?CarbonImmutable $created_at,
        public ?CarbonImmutable $updated_at,
        #[DataCollectionOf(ComparisonDetailData::class)]
        public Lazy|Collection|null $details,
        public ComparisonStatus $status = ComparisonStatus::PROGRESS
    ) {}

    public static function fromModel(Comparison $comparison): self
    {
        return new self(
            $comparison->id,
            $comparison->number_display,
            $comparison->order_date,
            $comparison->created_by,
            $comparison->updated_by,
            $comparison->created_at,
            $comparison->updated_at,
            Lazy::whenLoaded("details", $comparison, fn() => ComparisonDetailData::collect($comparison->details)),
            $comparison->status
        );
    }
}
