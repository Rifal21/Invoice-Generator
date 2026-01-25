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
        Schema::table('attendance_settings', function (Blueprint $table) {
            // Office location for geofencing
            $table->decimal('office_latitude', 10, 7)->nullable()->after('check_out_time');
            $table->decimal('office_longitude', 10, 7)->nullable()->after('office_latitude');
            $table->integer('allowed_radius')->default(100)->after('office_longitude'); // in meters

            // Security options
            $table->boolean('require_photo')->default(false)->after('allowed_radius');
            $table->boolean('require_location')->default(false)->after('require_photo');
            $table->boolean('strict_time')->default(true)->after('require_location'); // Can't check in too early
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn([
                'office_latitude',
                'office_longitude',
                'allowed_radius',
                'require_photo',
                'require_location',
                'strict_time',
            ]);
        });
    }
};
