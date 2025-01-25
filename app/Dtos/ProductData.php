<?php

namespace App\Dtos;

use App\Enums\ProductReorderLevel;
use App\Models\Category;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Lazy;

class ProductData extends Data
{
    public function __construct(
        #[FromRouteParameter('productId')]
        #[Rule("uuid")]
        public ?string $id,
        #[Rule("uuid")]
        public string $category_id,
        public Lazy|CategoryData|null $category,
        public string $name,
        public float $price,
        public ?string $description,
        public ProductReorderLevel $reorder_level = ProductReorderLevel::MEDIUM,
        public int $is_active = 1,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            $product->id,
            $product->category_id,
            Lazy::whenLoaded("category", $product, fn() => CategoryData::from($product->category)),
            $product->name,
            $product->price,
            $product->description,
            $product->reorder_level,
            $product->is_active,
        );
    }
}
