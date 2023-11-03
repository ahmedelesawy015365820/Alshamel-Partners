<?php

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\RealEstate\Entities\RealestateCustomTable;

class RealestateCustomTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $data = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'rlst')) {
                    array_push($data, $value);
                }
            }
        }

        foreach ($data as $item){
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $data2 = [];
            foreach ($columns as $column) {
                array_push($data2, ['column_name' => $column->Field,'is_required' => 1, 'is_visible' => 1]);
            }
            RealestateCustomTable::create([
                'table_name' => $item,
                'columns' => $data2,
                'company_id' => 0
            ]);
        }
    }
}
