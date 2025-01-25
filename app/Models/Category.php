<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $table = "categories";
    protected $fillable = ["name", "description"];
    protected $casts = [
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];

    protected static function newFactory(): Factory
    {
        return CategoryFactory::new();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shelfs()
    {
        return $this->hasMany(Shelf::class);
    }
}
