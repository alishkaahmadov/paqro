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
        Schema::create('highways', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('product__entry_id')->constrained('product_entries')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('pdf_file');
            $table->timestamp('entry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highways');
    }
};
