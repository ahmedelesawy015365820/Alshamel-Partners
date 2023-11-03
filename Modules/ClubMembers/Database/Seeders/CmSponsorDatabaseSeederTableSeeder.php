<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMemberType;
use Modules\ClubMembers\Entities\CmSponser;

class CmSponsorDatabaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cm_sponsers')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'بدون عضو',
                'name_e' => 'No Sponsor',

            ]

        ];

        foreach ($attributes as $attribute) {
            CmSponser::create($attribute);
        }

    }
}
