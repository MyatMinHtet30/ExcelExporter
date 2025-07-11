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
            $table->id(); // Condo_Detail_Id
            $table->foreignId('condo_id')->constrained('condos')->onDelete('cascade'); // CQ_Id
            $table->integer('no')->nullable();
            $table->string('details')->nullable(); // typo fixed
            $table->integer('amount')->nullable();
            $table->string('unit')->nullable();
            $table->float('material_cost')->nullable(); // typo fixed
            $table->float('labor_price')->nullable();
            $table->float('price_per_unit_total')->nullable();
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
