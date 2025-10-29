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
        Schema::create('homes', function (Blueprint $table) {
            $table->id(); // Home_Id
            $table->string('project_name')->nullable();
            $table->string('dear')->nullable();
            $table->date('date')->nullable();
            $table->string('trooper')->nullable();
            $table->string('house_no')->nullable();
            $table->string('list_name')->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
