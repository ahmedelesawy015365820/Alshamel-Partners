<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\AttendanceSetting;

class HrAttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hr_attendance_settings')->delete();

        $attributes = [
            [
                'id' => 1,
                'pre_in' => 30,
                'post_in' => 15,
                'absent_minutes' => 60,
                'pre_out' => 0,
                'post_out' => 30,
                'max_out' => 120,
            ],
        ];

        foreach ($attributes as $attribute) {
            AttendanceSetting::create($attribute);
        }

    }
}
