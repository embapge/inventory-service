<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, HasUuids;

    protected $table = "suppliers";
    protected $fillable = ["name", "phone", "email", "address"];
    protected $attributes = [
        "phone" => "[]",
        "email" => "[]",
    ];
    protected $casts = [
        "phone" => AsCollection::class,
        "email" => AsCollection::class,
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, "supplier_id", "id");
    }
}
