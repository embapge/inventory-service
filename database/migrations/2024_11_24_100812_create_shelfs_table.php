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
        Schema::create('shelfs', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->char("code", 10);
            $table->integer("capacity")->default(0);
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->timestamps();
            $table->foreignUuid('warehouse_id')->constrained(
                table: 'warehouses',
                indexName: 'shelfs_warehouse_id'
            )->onUpdate("cascade")->onDelete("restrict");
            $table->foreignUuid('category_id')->constrained(
                table: 'categories',
                indexName: 'shelfs_category_id'
            )->onUpdate("cascade")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelfs');
    }
};
