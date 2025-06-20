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
        Schema::table('product_entries', function (Blueprint $table) {
            $table->integer('limit')->default(0);
            $table->boolean('is_ordered')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_entries', function (Blueprint $table) {
            $table->dropColumn(['limit', 'is_ordered']);
        });
    }
};
