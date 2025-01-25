<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Stock extends Pivot
{
    use HasFactory;

    protected $table = "stocks";
    protected $fillable = ["qty", "product_id", "shelf_id"];
    protected $casts = [
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }
    // Jika inbound, outbound maka qty di update dan buatkan log.
    // Hitung qty berdasarkan stockmovement dengan kategori inbound, outbound.
}
