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
        Schema::create('condos', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('address')->nullable();
            $table->string('job_name')->nullable();
            $table->integer('quotation_number')->nullable();
            $table->date('date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('credit')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condos');
    }
};
