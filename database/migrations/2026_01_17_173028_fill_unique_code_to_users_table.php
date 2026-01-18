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
        $users = \App\Models\User::whereNull('unique_code')->get();
        foreach ($users as $user) {
            $code = 'JR-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            while (\App\Models\User::where('unique_code', $code)->exists()) {
                $code = 'JR-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            }
            $user->update(['unique_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
