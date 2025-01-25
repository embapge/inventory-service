<?php

namespace App\Resources;

use App\Enums\ComparisonStatus;
use App\Models\Comparison;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;

class ComparisonResource extends Resource
{
    public function __construct(
        public string $id,
        public string $number_display,
        public CarbonImmutable $order_date,
        public Lazy|Collection $details,
        public ComparisonStatus $status = ComparisonStatus::PROGRESS
    ) {}

    public static function fromModel(Comparison $comparison): self
    {
        return new self(
            $comparison->id,
            $comparison->number_display,
            CarbonImmutable::parse($comparison->order_date),
            Lazy::whenLoaded("details", $comparison, fn() => ComparisonDetailResource::collect($comparison->details)),
            $comparison->status
        );
    }
}
