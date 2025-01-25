<?php

namespace App\Dtos;

use App\Enums\StockMovementStatus;
use App\Models\StockMovement;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

class StockMovementProgressData extends Data
{
  public function __construct(
    #[FromRouteParameter('stockMovementId')]
    #[Rule("uuid")]
    public string $stock_movement_id,
    public string $reason,
    public StockMovementStatus $status = StockMovementStatus::PROCESS,
  ) {}
}
