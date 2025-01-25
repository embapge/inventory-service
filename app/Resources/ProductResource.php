<?php

namespace App\Resources;

use App\Enums\ProductReorderLevel;
use App\Models\Product;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;

class ProductResource extends Resource
{
    public function __construct(
        public string $id,
        public string $category_id,
        public CategoryResource $category,
        public string $name,
        public string $price,
        public string $is_active,
        public ?string $description,
        public ProductReorderLevel $reorder_level,
        public Lazy|Collection|null $shelfs,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            $product->id,
            $product->category_id,
            CategoryResource::from($product->category),
            $product->name,
            $product->price,
            $product->is_active,
            $product->description,
            $product->reorder_level,
            Lazy::whenLoaded(
                "shelfs",
                $product,
                fn() => ShelfResource::collect(
                    $product->load(["shelfs.warehouse"])->shelfs
                )
            )
        );
    }
}
