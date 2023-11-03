<?php

namespace Database\Seeders;

use App\Models\GeneralCustomTable;
use Illuminate\Database\Seeder;

class CustomTableSeeder extends Seeder
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
                if (str_contains($value, 'general')) {
                    array_push($data, $value);
                }
            }
        }
        foreach ($data as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $data2 = [];
            foreach ($columns as $column) {
                array_push($data2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $data2,
                'company_id' => 0,
                'type' => 'General',
            ]);
        }

        $boards = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'boards')) {
                    array_push($boards, $value);
                }
            }
        }
        foreach ($boards as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $boards2 = [];
            foreach ($columns as $column) {
                array_push($boards2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $boards2,
                'company_id' => 0,
                'type' => 'Board Rent',
            ]);
        }

        $cm = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'cm')) {
                    array_push($cm, $value);
                }
            }
        }
        foreach ($cm as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $cm2 = [];
            foreach ($columns as $column) {
                array_push($cm2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $cm2,
                'company_id' => 0,
                'type' => 'Club Members',
            ]);
        }

        $hr = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'hr')) {
                    array_push($hr, $value);
                }
            }
        }
        foreach ($hr as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $hr2 = [];
            foreach ($columns as $column) {
                array_push($hr2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $hr2,
                'company_id' => 0,
                'type' => 'Human Resources',
            ]);
        }

        $rlst = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'rlst')) {
                    array_push($rlst, $value);
                }
            }
        }
        foreach ($rlst as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $rlst2 = [];
            foreach ($columns as $column) {
                array_push($rlst2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $rlst2,
                'company_id' => 0,
                'type' => 'Real Estate',
            ]);
        }

        $rp = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'rp')) {
                    array_push($rp, $value);
                }
            }
        }
        foreach ($rp as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $rp2 = [];
            foreach ($columns as $column) {
                array_push($rp2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $rp2,
                'company_id' => 0,
                'type' => 'Receivable Payable',
            ]);
        }

        $pointOfSale= [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'pos_')) {
                    array_push($pointOfSale, $value);
                }
            }
        }
        foreach ($pointOfSale as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $pointOfSale2 = [];
            foreach ($columns as $column) {
                array_push($pointOfSale2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $pointOfSale2,
                'company_id' => 0,
                'type' => 'Point Of Sale',
            ]);
        }

        $booking= [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'booking_')) {
                    array_push($booking, $value);
                }
            }
        }
        foreach ($booking as $item) {
            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
            $pointOfSale2 = [];
            foreach ($columns as $column) {
                array_push($pointOfSale2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
            }
            GeneralCustomTable::create([
                'table_name' => $item,
                'columns' => $pointOfSale2,
                'company_id' => 0,
                'type' => 'Booking',
            ]);
        }

//        $data = [];
//        foreach ($tables as $table) {
//            foreach ($table as $key => $value) {
//                if (str_contains($value, 'cus')) {
//                    array_push($data, $value);
//                }
//            }
//        }
//        foreach ($data as $item) {
//            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
//            $data2 = [];
//            foreach ($columns as $column) {
//                array_push($data2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
//            }
//            GeneralCustomTable::create([
//                'table_name' => $item,
//                'columns' => $data2,
//                'company_id' => 0,
//                'type' => 'Custody',
//            ]);
//        }

//        $data = [];
//        foreach ($tables as $table) {
//            foreach ($table as $key => $value) {
//                if (str_contains($value, 'stock')) {
//                    array_push($data, $value);
//                }
//            }
//        }
//        foreach ($data as $item) {
//            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
//            $data2 = [];
//            foreach ($columns as $column) {
//                array_push($data2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
//            }
//            GeneralCustomTable::create([
//                'table_name' => $item,
//                'columns' => $data2,
//                'company_id' => 0,
//                'type' => 'Stock',
//            ]);
//        }

//        $data = [];
//        foreach ($tables as $table) {
//            foreach ($table as $key => $value) {
//                if (str_contains($value, 'arch')) {
//                    array_push($data, $value);
//                }
//            }
//        }
//        foreach ($data as $item) {
//            $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $item);
//            $data2 = [];
//            foreach ($columns as $column) {
//                array_push($data2, ['column_name' => $column->Field, 'is_required' => 1, 'is_visible' => 1]);
//            }
//            GeneralCustomTable::create([
//                'table_name' => $item,
//                'columns' => $data2,
//                'company_id' => 0,
//                'type' => 'Archiving',
//            ]);
//        }

    }
}
