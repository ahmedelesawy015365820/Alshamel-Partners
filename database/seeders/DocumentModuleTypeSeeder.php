<?php

namespace Database\Seeders;

use App\Models\DocumentModuleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentModuleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $DocumentModuleTypes = [
            [
                'name'     => 'وحدات' ,
                'name_e'   => 'units',
                'title'    => 'رقم الوحده',
                'title_e'  =>  'unit_num',
                'db_table' => 'rlst_units',

            ]

        ];
        foreach($DocumentModuleTypes as $ModuleTypes){
            DocumentModuleType::create($ModuleTypes);
        }
    }
}
