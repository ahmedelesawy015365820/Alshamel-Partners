<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_settings')->delete();

        $data = [
            [
                'key'   => 'Check-in',
                'value' => Carbon::createFromTime(13, 0)->format('H:i'),
            ],
            [
                'key' => 'Check-out',
                'value' => Carbon::createFromTime(12, 0)->format('H:i'),
            ],
        ];

        DB::table('booking_settings')->insert($data);
    }
}
