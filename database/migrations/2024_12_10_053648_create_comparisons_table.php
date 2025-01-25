<?php

use App\Enums\ComparisonStatus;
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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->string("number_display")->nullable();
            $table->date("order_date");
            $table->enum("status", array_column(ComparisonStatus::cases(), 'value'))->default(ComparisonStatus::PROGRESS);
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
        Schema::dropIfExists('comparisons');
    }
};
