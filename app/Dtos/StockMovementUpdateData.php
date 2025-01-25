<?php

namespace App\Dtos;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class StockMovementUpdateData extends Data
{
  public function __construct(
    #[FromRouteParameter("stockMovementId")]
    #[Rule("uuid")]
    public string $stock_movement_id,
    #[MapInputName(SnakeCaseMapper::class)]
    public ?StockMovement $stockMovement,
    #[RequiredWithout("movement_date")]
    public ?StockMovementType $type,
    #[RequiredWithout("type")]
    public ?CarbonImmutable $movement_date
  ) {}
}
