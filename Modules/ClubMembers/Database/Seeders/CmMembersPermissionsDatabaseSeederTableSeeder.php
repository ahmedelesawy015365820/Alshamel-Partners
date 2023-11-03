<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMemberPermission;

class CmMembersPermissionsDatabaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cm_members_permissions')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'حضور الانتخابات',
                'name_e' => 'ٌAttending Elections',

            ],
            [
                'id' => 2,
                'name' => 'تصويت في الانتخابات',
                'name_e' => 'Voting in Elections',

            ],
            [
                'id' => 3,
                'name' => 'الترشح في الانتخابات',
                'name_e' => 'Potential Candidate in Elections',

            ],

        ];

        foreach ($attributes as $attribute) {
            CmMemberPermission::create($attribute);
        }

    }
}
