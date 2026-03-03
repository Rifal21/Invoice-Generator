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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('amount');
            $table->string('payment_channel')->nullable()->after('status');
            $table->string('snap_token')->nullable()->after('reference_id');
            $table->string('payment_url')->nullable()->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_channel', 'snap_token', 'payment_url']);
        });
    }
};
