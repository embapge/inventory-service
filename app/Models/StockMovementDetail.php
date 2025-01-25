<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = "stock_movement_details";
    protected $fillable = ["stock_movement_id", "product_id", "qty", "logs", "created_by", "updated_by"];
    protected $casts = [
        "logs" => AsCollection::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];
    protected $attributes = [
        "logs" => "[]"
    ];

    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class, "stock_movement_id", "id");
    }

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }
}
