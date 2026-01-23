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
        Schema::create('vehicle_rental_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_rental_invoice_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->decimal('quantity', 15, 2);
            $table->string('unit');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_rental_items');
    }
};
