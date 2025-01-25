<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PurchaseOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = "purchase_orders";
    protected $fillable = ["supplier_id", "order_date", "total_amount", "status"];
    protected $casts = [
        "status" => PurchaseOrderStatus::class,
        "order_date" => "immutable_date",
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];
    public static $relationship = ["supplier", "details", "comparisonDetails", "stockMovements"];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, "supplier_id", "id");
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, "purchase_order_id", "id");
    }

    public function comparisonDetails()
    {
        return $this->hasMany(ComparisonDetail::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, "stockable");
    }

    public function calculate()
    {
        return true;
    }
}
