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
        Schema::table('highways', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn(['product_entry_id', 'quantity', 'pdf_file', 'entry_date']);
            
            // Add new columns
            $table->foreignId('belong_to_warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('highways', function (Blueprint $table) {
            // Add dropped columns back
            $table->foreignId('product_entry_id')->constrained('product_entries')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('pdf_file')->nullable();
            $table->timestamp('entry_date');
            
            // Drop new columns
            $table->dropColumn(['belong_to_warehouse_id']);
        });
    }
};
