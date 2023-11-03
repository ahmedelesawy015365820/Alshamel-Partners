<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmFinancialStatus;
use Modules\ClubMembers\Entities\CmMemberType;

class CmFinancialStatusDatabaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cm_financial_status')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'مسدد',
                'name_e' => 'Paid',
            ],
            [
                'id' => 2,
                'name' => 'غير مسدد',
                'name_e' => 'Un Paid',
            ],


        ];

        foreach ($attributes as $attribute) {
            CmFinancialStatus::create($attribute);
        }

    }
}
