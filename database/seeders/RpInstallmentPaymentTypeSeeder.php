<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentType;

class RpInstallmentPaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RpInstallmentPaymentType::create([
            "name"=>"دفعة مستحقة",
            "name_e"=>"Payment due",
            "installment_payment_type_freq"=>0,
            "freq_period" => 0 ,
        ]);
    }
}
