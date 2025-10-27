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
        Schema::create('home_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('home_id')->constrained('homes')->cascadeOnDelete();
            $table->boolean('status')->default(true);

            $table->unsignedInteger('no')->nullable();
            $table->string('category_name')->nullable();
            $table->string('item_name')->nullable(); // Enter item name

            // quantities & prices
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('mc_price', 12, 2)->nullable(); // Material price / unit
            $table->decimal('lc_price', 12, 2)->nullable(); // Labor price / unit

            // optional: store computed totals for convenience
            $table->decimal('material_total', 14, 2)->nullable();
            $table->decimal('labor_total', 14, 2)->nullable();
            $table->decimal('grand_total', 14, 2)->nullable();

            $table->timestamps();

            $table->index(['home_id', 'category_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_details'); // fixed typo
    }
};
