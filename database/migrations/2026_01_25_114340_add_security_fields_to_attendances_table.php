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
        Schema::table('attendances', function (Blueprint $table) {
            // GPS Location tracking
            $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in');
            $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');

            // Photo verification
            $table->string('check_in_photo')->nullable()->after('check_out_longitude');
            $table->string('check_out_photo')->nullable()->after('check_in_photo');

            // IP Address & Device tracking
            $table->string('check_in_ip')->nullable()->after('check_out_photo');
            $table->string('check_out_ip')->nullable()->after('check_in_ip');
            $table->text('check_in_user_agent')->nullable()->after('check_out_ip');
            $table->text('check_out_user_agent')->nullable()->after('check_in_user_agent');
            $table->string('check_in_device_id')->nullable()->after('check_out_user_agent');
            $table->string('check_out_device_id')->nullable()->after('check_in_device_id');

            // Distance from allowed location
            $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_out_device_id'); // in meters
            $table->decimal('check_out_distance', 8, 2)->nullable()->after('check_in_distance'); // in meters

            // Approval system for corrections
            $table->boolean('is_manual_entry')->default(false)->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('is_manual_entry');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('correction_reason')->nullable()->after('approved_at');

            // Add index for performance
            $table->index(['user_id', 'date']);
            $table->index('check_in_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude',
                'check_in_longitude',
                'check_out_latitude',
                'check_out_longitude',
                'check_in_photo',
                'check_out_photo',
                'check_in_ip',
                'check_out_ip',
                'check_in_user_agent',
                'check_out_user_agent',
                'check_in_device_id',
                'check_out_device_id',
                'check_in_distance',
                'check_out_distance',
                'is_manual_entry',
                'correction_reason',
                'approved_at',
            ]);

            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
        });
    }
};
