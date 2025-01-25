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
        Schema::create('stocks', function (Blueprint $table) {
            $table->integer("qty")->default(0);
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->foreignUuid('shelf_id')->constrained(
                table: 'shelfs',
                indexName: 'stocks_shelf_id'
            )->onUpdate("cascade")->onDelete("restrict");
            $table->foreignUuid('product_id')->constrained(
                table: 'products',
                indexName: 'stocks_product_id'
            )->onUpdate("cascade")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
