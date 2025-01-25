<?php

use App\Enums\ComparisonDetailStatus;
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
        Schema::create('comparison_details', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->enum("status", array_column(ComparisonDetailStatus::cases(), "value"))->default(ComparisonDetailStatus::PENDING);
            $table->foreignUuid('comparison_id')->constrained(
                table: 'comparisons',
                indexName: 'comparison_details_comparison_id'
            )->onUpdate("cascade")->onDelete("cascade");
            $table->foreignUuid('purchase_order_id')->constrained(
                table: 'purchase_orders',
                indexName: 'comparison_details_purchase_order_id'
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
        Schema::dropIfExists('comparison_details');
    }
};
