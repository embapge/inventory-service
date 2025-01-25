<?php

use App\Enums\ProductReorderLevel;
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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->string("name", 50);
            $table->float("price", 20, 2);
            $table->integer("is_active")->default(1);
            $table->text("description")->nullable();
            $table->enum("reorder_level", array_column(ProductReorderLevel::cases(), 'value'));
            $table->timestamps();
            $table->uuid("created_by")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->foreignUuid('category_id')->constrained(
                table: 'categories',
                indexName: 'products_category_id'
            )->onUpdate("cascade")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
