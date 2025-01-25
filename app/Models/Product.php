<?php

namespace App\Models;

use App\Enums\ProductReorderLevel;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $table = "products";
    protected $fillable = ["id", "category_id", "category", "name", "price", "is_active", "description", "reorder_level"];
    protected $casts = [
        "reorder_level" => ProductReorderLevel::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function shelfs(): BelongsToMany
    {
        return $this->belongsToMany(Shelf::class, "stocks", "product_id", "shelf_id")->withPivot('qty', 'created_by', 'updated_by')->withTimestamps()->using(Stock::class);
    }
}
