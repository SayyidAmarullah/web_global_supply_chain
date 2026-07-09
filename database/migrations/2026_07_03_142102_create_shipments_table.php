<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('shipment_number')->unique();
            $table->enum('type', ['import', 'export']);
            $table->string('commodity');
            $table->decimal('quantity', 15, 2);
            $table->string('unit');
            $table->string('container_type');
            $table->string('container_number')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('imo_number')->nullable();
            $table->string('origin_country');
            $table->string('origin_port');
            $table->string('destination_country');
            $table->string('destination_port');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->decimal('current_speed', 8, 2)->nullable();
            $table->decimal('current_heading', 5, 2)->nullable();
            $table->string('status')->default('Pending');
            $table->date('departure_date')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->date('actual_arrival')->nullable();
            $table->decimal('insurance_value', 15, 2)->nullable();
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->decimal('cargo_value', 15, 2)->nullable();
            $table->decimal('estimated_revenue', 15, 2)->nullable();
            $table->decimal('estimated_profit', 15, 2)->nullable();
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('shipment_redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('old_destination_country');
            $table->string('old_destination_port');
            $table->string('new_destination_country');
            $table->string('new_destination_port');
            $table->text('reason');
            $table->date('old_estimated_arrival')->nullable();
            $table->date('new_estimated_arrival')->nullable();
            $table->decimal('old_shipping_cost', 15, 2)->nullable();
            $table->decimal('new_shipping_cost', 15, 2)->nullable();
            $table->decimal('old_estimated_profit', 15, 2)->nullable();
            $table->decimal('new_estimated_profit', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('description');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_activities');
        Schema::dropIfExists('shipment_redirects');
        Schema::dropIfExists('shipments');
    }
};
