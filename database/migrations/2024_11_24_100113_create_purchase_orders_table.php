<?php

use App\Enums\PurchaseOrderStatus;
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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->date("order_date");
            $table->float("total_amount", 20, 2);
            $table->enum("status", array_column(PurchaseOrderStatus::cases(), 'value'))->default("pending");
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->foreignUuid('supplier_id')->constrained(
                table: 'suppliers',
                indexName: 'purchase_orders_supplier_id'
            )->onUpdate("cascade")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
