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
        Schema::create('kitchen_incentives', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('date');
            $table->string('recipient_name')->default('SPPG Dapur Kabungah Cipondok');
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });

        Schema::create('kitchen_incentive_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitchen_incentive_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('quantity', 10, 2);
            $table->string('unit'); // e.g., 'Hari'
            $table->string('duration_text')->nullable(); // e.g., '(26 Jan - 31 Jan 2026)'
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_incentive_items');
        Schema::dropIfExists('kitchen_incentives');
    }
};
