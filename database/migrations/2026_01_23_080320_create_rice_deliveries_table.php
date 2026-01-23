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
        Schema::create('rice_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('nota_number')->unique();
            $table->string('location')->default('Ciawi');
            $table->date('date');
            $table->text('customer_name');
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rice_deliveries');
    }
};
