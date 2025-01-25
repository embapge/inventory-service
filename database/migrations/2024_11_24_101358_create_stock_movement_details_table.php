<?php

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
        Schema::create('stock_movement_details', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->integer("qty");
            $table->json("logs");
            $table->foreignUuid('stock_movement_id')->constrained(
                table: 'stock_movements',
                indexName: 'stock_movement_details_stock_movement_id'
            )->onUpdate("cascade")->onDelete("restrict");
            $table->foreignUuid('product_id')->constrained(
                table: 'products',
                indexName: 'stock_movement_details_product_id'
            )->onUpdate("cascade")->onDelete("restrict");
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement_details');
    }
};
