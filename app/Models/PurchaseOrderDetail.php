<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class PurchaseOrderDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = "purchase_order_details";
    protected $fillable = ["product", "qty", "total", "product_id", "category_id"];
    protected $casts = [
        "product" => AsCollection::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, "purchase_order_id", "id");
    }
}
