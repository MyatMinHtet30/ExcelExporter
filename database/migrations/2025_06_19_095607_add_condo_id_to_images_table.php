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
        Schema::table('images', function (Blueprint $table) {
            // Make home_id nullable since images can belong to either home or condo
            $table->foreignId('home_id')->nullable()->change();
            
            // Add condo_id column
            $table->foreignId('condo_id')->nullable()->after('home_id')->constrained('condos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['condo_id']);
            $table->dropColumn('condo_id');
            
            // Restore home_id to not nullable
            $table->foreignId('home_id')->nullable(false)->change();
        });
    }
};
