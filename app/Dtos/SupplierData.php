<?php

namespace App\Dtos;

use App\Models\Supplier;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

class SupplierData extends Data
{
    public function __construct(
        #[FromRouteParameter('supplierId')]
        #[Rule("uuid")]
        public ?string $id,
        public string $name,
        public array|Collection $phone,
        public array|Collection $email,
        public string $address
    ) {}

    public static function fromModel(Supplier $supplier): self
    {
        return new self(
            $supplier->id,
            $supplier->name,
            $supplier->phone,
            $supplier->email,
            $supplier->address
        );
    }
}
