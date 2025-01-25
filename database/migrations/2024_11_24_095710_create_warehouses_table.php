<?php

use App\Enums\WarehouseType;
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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->uuid("id");
            $table->primary("id");
            $table->string("name", 50);
            $table->enum("type", array_column(WarehouseType::cases(), 'value'));
            $table->text("location");
            $table->text("lat");
            $table->text("lang");
            $table->integer("capacity")->default(0);
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
        Schema::dropIfExists('warehouses');
    }
};
