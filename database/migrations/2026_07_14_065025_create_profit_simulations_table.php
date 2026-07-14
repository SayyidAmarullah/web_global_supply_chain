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
        Schema::create('profit_simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->string('name')->nullable(); // Optional simulation name
            
            // Inputs
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('import_tax', 15, 2)->default(0);
            $table->decimal('export_tax', 15, 2)->default(0);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            
            // Outputs
            $table->decimal('expected_revenue', 15, 2)->default(0);
            $table->decimal('expected_profit', 15, 2)->default(0);
            $table->decimal('margin_percentage', 5, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_simulations');
    }
};
