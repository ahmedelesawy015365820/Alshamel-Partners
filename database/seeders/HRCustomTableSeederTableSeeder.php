<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\HR\Entities\HRCustomTable;

class HRCustomTableSeederTableSeeder extends Seeder
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
                if (str_contains($value, 'hr')) {
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
            HRCustomTable::create([
                'table_name' => $item,
                'columns' => $data2,
                'company_id' => 0
            ]);
        }
    }
}
