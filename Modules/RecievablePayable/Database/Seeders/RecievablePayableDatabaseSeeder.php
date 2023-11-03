<?php

namespace Modules\RecievablePayable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentType;

class RecievablePayableDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        RpInstallmentPaymentType::create([
            "name"=>"دفعة مستحقة",
            "name_e"=>"Payment due",
        ]);


    }
}
