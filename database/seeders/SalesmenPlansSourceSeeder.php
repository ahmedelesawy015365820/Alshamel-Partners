<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SalesmenPlansSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'name' => 'مباشر',
                'name_e' => "direct",
            ],
            [
                'name' => "غير مباشر",
                'name_e' => "indirect",
            ],
            [
                'name' => "كلي",
                'name_e' => "total",
            ],

        ];

        foreach ($data as $item) {
            \App\Models\SalesmenPlansSource::create($item);
        }
    }
}
