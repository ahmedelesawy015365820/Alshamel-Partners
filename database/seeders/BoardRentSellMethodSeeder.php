<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\BoardsRent\Entities\SellMethod;

class BoardRentSellMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SellMethod::create([
            'name' =>'متنوع',
            'name_e' =>'various',
            'is_default' =>1,
            'is_all_value' =>0,
        ]);
    }
}
