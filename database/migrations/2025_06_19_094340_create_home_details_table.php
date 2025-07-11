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
            $table->boolean('status')->default(true);
            $table->integer('no')->nullable();
            $table->foreignId('home_id')->constrained('homes')->onDelete('cascade');
            $table->string('category_name')->nullable();
            $table->integer('amount')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('mc_price')->nullable();
            $table->decimal('lc_price')->nullable(); // <-- this was missing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_deatils');
    }
};
