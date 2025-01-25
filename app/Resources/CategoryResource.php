<?php

namespace App\Resources;

use App\Models\Category;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;

class CategoryResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
    ) {}

    public static function fromModel(Category $category): self
    {
        return new self(
            $category->id,
            $category->name,
            $category->description,
        );
    }
}
