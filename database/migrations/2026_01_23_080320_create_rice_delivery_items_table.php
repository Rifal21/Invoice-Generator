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
        Schema::create('rice_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rice_delivery_id')->constrained()->onDelete('cascade');
            $table->string('quantity_string'); // e.g. "800 kg"
            $table->text('description');
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
        Schema::dropIfExists('rice_delivery_items');
    }
};
