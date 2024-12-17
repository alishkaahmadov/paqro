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
        Schema::create('highway_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('highway_id')->constrained('highways')->onDelete('cascade');
            $table->foreignId('product_entry_id')->constrained('product_entries')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('pdf_file')->nullable();
            $table->timestamp('entry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highway_products');
    }
};
