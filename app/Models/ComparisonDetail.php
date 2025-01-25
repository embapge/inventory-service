<?php

namespace App\Models;

use App\Enums\ComparisonDetailStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = "comparison_details";
    protected $fillable = ["purchase_order_id", "comparison_id", "status", "created_by", "updated_by"];
    protected $casts = [
        "status" => ComparisonDetailStatus::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function comparison()
    {
        return $this->belongsTo(Comparison::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
