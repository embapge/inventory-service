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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->json("product");
            $table->integer("qty");
            $table->float("total", 20, 2)->default(0);
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->foreignUuid('purchase_order_id')->constrained(
                table: 'purchase_orders',
                indexName: 'purchase_order_details_purchase_order_id'
            )->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
    }
};
