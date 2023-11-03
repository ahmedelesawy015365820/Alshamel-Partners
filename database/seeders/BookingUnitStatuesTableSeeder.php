<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Entities\UnitStatus;

class BookingUnitStatuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_unit_statuses')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'متاح',
                'name_e' => 'free',
            ],
            [
                'id' => 2,
                'name' => 'محجوز',
                'name_e' => 'reserved',
            ],
            [
                'id' => 3,
                'name' => 'تحت الصيانه',
                'name_e' => 'under maintenance',
            ],
        ];

        foreach ($attributes as $attribute) {
            UnitStatus::create($attribute);
        }
    }
}
