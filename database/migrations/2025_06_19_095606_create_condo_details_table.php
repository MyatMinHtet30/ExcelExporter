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

        Schema::create('condo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condo_id')->constrained('condos')->cascadeOnDelete();
            $table->integer('no')->nullable();
            $table->string('details')->nullable();
            $table->decimal('amount', 18, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('material_cost', 18, 2)->nullable();
            $table->decimal('labor_cost', 18, 2)->nullable();
            $table->decimal('price_per_unit_total', 18, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condo_deatils');
    }
};
