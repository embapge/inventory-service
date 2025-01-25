<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class StockMovement extends Model
{
    use HasFactory, HasUuids;

    protected $table = "stock_movements";
    protected $fillable = ["warehouse_id", "stockable_id", "stockable_type", "type", "movement_date", "status", "created_by", "updated_by"];
    protected $casts = [
        "status" => AsCollection::class,
        "type" => StockMovementType::class,
        "movement_date" => "immutable_datetime",
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function lastStatus()
    {
        return $this->status->last();
    }

    public function statuses(): Collection
    {
        return $this->status;
    }

    public function details()
    {
        return $this->hasMany(StockMovementDetail::class, "stock_movement_id", "id");
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }
}
