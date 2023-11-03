<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_branches')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'رئيسي',
                'name_e' => 'Main',
                'is_active' => 1,

            ],

        ];

        foreach ($attributes as $attribute) {
            Branch::create($attribute);
        }
    }
}
