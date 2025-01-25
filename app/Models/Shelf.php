<?php

namespace App\Models;

use App\Observers\ShelfObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([ShelfObserver::class])]
class Shelf extends Model
{
    use HasFactory, HasUuids;

    protected $table = "shelfs";
    protected $fillable = ["code", "capacity", "warehouse_id", "category_id"];
    protected $casts = [
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, "warehouse_id", "id");
    }

    public function category()
    {
        return $this->belongsTo(category::class, "category_id", "id");
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, "stocks", "shelf_id", "product_id")->withPivot('qty', 'created_by', 'updated_by')->withTimestamps()->using(Stock::class);
        // return $this->belongsToMany(Product::class, "stocks", "shelf_id", "product_id")->withPivot(["qty"]);
    }
}
