<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMemberType;

class CmMembersTypesDatabaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cm_members_types')->delete();

        $attributes = [
               
                [
                    'id' => 1,
                    'name' => 'عضو عادي',
                    'name_e' => 'ٌRegular Member',
                    'parent_id' => 1,
                    
                ],
                
                [
                    'id' => 2,
                    'name' => 'عضو مؤسس',
                    'name_e' => 'Founding Member',
                    'parent_id' => 1,
                    
                ],
                [
                    'id' => 3,
                    'name' => 'عضو شرفي',
                    'name_e' => 'Honorary Member',
                    'parent_id' => 1,
                    
                ],
                [
                    'id' => 4,
                    'name' => 'عضو مشطوب',
                    'name_e' => 'Canceled member',
                    'parent_id' => 1,
                    
                ],
                [
                    'id' => 5,
                    'name' => 'عضو موقوف',
                    'name_e' => 'suspended member',
                    'parent_id' => 1,
                    
                ],
    

        ];

        foreach ($attributes as $attribute) {
            CmMemberType::create($attribute);
        }

    }
}
