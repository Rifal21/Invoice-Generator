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
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('permissions')->nullable(); // comma separated roles or can be json
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('sidebar_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_items');
    }
};
