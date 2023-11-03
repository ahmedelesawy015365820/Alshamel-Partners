<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\RecievablePayable\Entities\RpInstallmentStatus;

class RpInstallmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RpInstallmentStatus::create([
            'name' =>"غير مدفوع",
            'name_e' =>"Unpaid",
        ]);
    }
}
