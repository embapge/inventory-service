<?php

namespace App\Dtos;

use App\Models\Category;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class CategoryData extends Data
{
    public function __construct(
        #[FromRouteParameter('categoryId')]
        #[Rule("uuid")]
        public ?string $id,
        public string $name,
        public ?string $description,
        public ?string $created_by,
        public ?string $updated_by,
        public ?CarbonImmutable $created_at,
        public ?CarbonImmutable $updated_at,
        public Lazy|Collection|null $products
    ) {}

    public static function fromModel(Category $category): self
    {
        return new self(
            $category->id,
            $category->name,
            $category->description,
            $category->created_by,
            $category->updated_by,
            $category->updated_at,
            $category->created_at,
            Lazy::whenLoaded("products", $category, fn() => ProductData::collect($category->products)),
        );
    }
}
