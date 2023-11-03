<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\Status;

class HrStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('hr_statuses')->delete();

        $attributes = [
           [
               'id' => 1,
               'name' => 'قيد الانتظار',
               'name_e' => 'pending',
           ],
           [
               'id' => 2,
               'name' => 'في تقدم',
               'name_e' => 'processing',
           ],
           [
               'id' => 3,
               'name' => 'تم الموافقه عليه',
               'name_e' => 'Accepted',
           ],
           [
               'id' => 4,
               'name' => 'تم رفضه',
               'name_e' => 'Rejected',
           ],


        ];

        foreach ($attributes as $attribute) {
            Status::create($attribute);
        }
    }

}
