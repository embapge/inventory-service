<?php

namespace App\Models;

use App\Enums\WarehouseType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Warehouse extends Model
{
    use HasFactory, HasUuids;

    protected $table = "warehouses";
    protected $fillable = ["id", "name", "type", "location", "lat", "lang", "capacity"];
    protected $casts = [
        "type" => WarehouseType::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function shelfs()
    {
        return $this->hasMany(Shelf::class, "warehouse_id", "id");
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, "stockable");
    }
}
