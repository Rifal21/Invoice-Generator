<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AttendanceSetting::updateOrCreate(
            ['id' => 1],
            [
                'check_in_time' => '08:00',
                'check_out_time' => '17:00'
            ]
        );
    }
}
