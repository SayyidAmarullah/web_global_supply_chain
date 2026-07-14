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
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('type'); // export, import, redirect
            $table->string('recommended_country')->nullable();
            $table->string('recommended_port')->nullable();
            $table->string('recommended_commodity')->nullable();
            
            // Financials
            $table->decimal('estimated_revenue', 15, 2)->default(0);
            $table->decimal('estimated_profit', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            
            // Scores
            $table->integer('risk_score')->default(0);
            $table->integer('opportunity_score')->default(0);
            $table->integer('confidence_score')->default(0);
            
            // Textual explanations
            $table->text('reason')->nullable();
            $table->text('advantages')->nullable();
            $table->text('disadvantages')->nullable();
            
            $table->string('status')->default('Pending'); // Accepted, Rejected, Pending
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
