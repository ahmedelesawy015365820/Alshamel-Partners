<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientTypes = [
            [
                'name'     => 'عميل',
                'name_e'   => 'customer',
                'db_table' => 'general_customers',
            ],
            [
                'name'     => 'مورد',
                'name_e'   => 'suppler',
                'db_table' => 'general_supplers',
            ],
            [
                'name'     => 'موظف',
                'name_e'   => 'employee',
                'db_table' => 'general_employees',
            ]

        ];



        foreach ($clientTypes as $type):
            ClientType::create($type);
        endforeach;
    }
}
