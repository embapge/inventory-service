<?php

namespace App\Models;

use App\Enums\ComparisonStatus;
use App\Observers\ComparisonObserver;
use Database\Factories\ComparisonFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ComparisonObserver::class])]
class Comparison extends Model
{
    use HasFactory, HasUuids;

    protected $table = "comparisons";
    protected $fillable = ["number_display", "order_date", "created_by", "updated_by"];
    protected $casts = [
        "status" => ComparisonStatus::class,
        "order_date" => "immutable_datetime",
        "created_at" => "immutable_datetime",
        "updated_at" => "immutable_datetime",
    ];


    protected static function newFactory(): Factory
    {
        return ComparisonFactory::new();
    }

    public function details()
    {
        return $this->hasMany(ComparisonDetail::class);
    }
}
