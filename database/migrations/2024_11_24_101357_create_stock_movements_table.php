<?php

use App\Enums\StockMovementType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->uuid("stockable_id");
            $table->text("stockable_type");
            $table->enum("type", array_column(StockMovementType::cases(), 'value'));
            $table->timestamp("movement_date")->nullable();
            $table->json("status");
            $table->timestamps();
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->foreignUuid('warehouse_id')->constrained(
                table: 'warehouses',
                indexName: 'stock_movements_warehouse_id'
            )->onUpdate("cascade")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
