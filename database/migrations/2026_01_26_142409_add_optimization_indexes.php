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
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('date');
            $table->index('invoice_number');
            $table->index('customer_name');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date');
            $table->index('category');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->index(['start_date', 'end_date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['customer_name']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['category']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropIndex(['start_date', 'end_date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['category_id']);
        });
    }
};
