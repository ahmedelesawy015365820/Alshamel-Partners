<?php

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\RealEstate\Entities\RlstPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class RlstPoliciesTableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('rlst_policies')->truncate();
       
        Model::unguard();

        $data = [
            [
                'id' => 1,
                'name' => "المالك له مبلغ ثابت",
                'name_e' => "Owner has a fixed amount",
                'description' => "الشركة حتدفع المبلغ و مازاد او قل الشركة حتتحملهااو تاخده",
            ],

            [
                'id' => 2,
                'name' => "الشركة لها حد ادني او نسبة",
                'name_e' => "Company has a fixed amount or percent",
                'description' => "الشركة هتاخد الحد الادني علي الاقل لكن لو المحصل اكتر من الحد الادني فالشركة هتاخد النسبة و الباقي للمالك ",            
            ],

            [
                'id' => 3,
                'name' => "الشركة لها نسبة ثابتة",
                'name_e' => "Company has a fixed percent",
                'description' => "الشركة ستحصل علي نسبتها و الباقي للمالك",
            ],

            [
                'id' => 4,
                'name' => "الشركة لها مبلغ ثابت",
                'name_e' => "Company has a fixed amount",
                'description' => "الشركة ستحصل علي مبلغ ثابت و الباقي للمالك",
            ],

            [
                'id' => 5,
                'name' => "الشركة لها مبلغ ثابت و نسبة لو زاد المبلغ الموزع  عن قيمة ثابتة",
                'name_e' => "Company has a fixed amount plus a percent if the amount increased beyond a certain value",
                'description' => "الشركة ستحصل علي مبلغ ثابت و لو الايراد تغطي مبلغ محدد ستحصل علي نسبة من الزيادة",
            ],



        ];

        foreach ($data as $record) {
            RlstPolicy::create($record);
        }

    } // run

} // class RlstPoliciesTableTableSeeder extends Seeder
