<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class CmBranchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    


    public function run()
    {
                /*
                    1	id	bigint(20) unsigned	NULL	NULL	NO	NULL	auto_increment		
                    2	company_id	int(10) unsigned	NULL	NULL	YES	NULL			
                    3	name	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
                    4	name_e	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
                    5	is_active	varchar(191)	utf8mb4	utf8mb4_unicode_ci	NO	inactive			
                    6	parent_id	int(10) unsigned	NULL	NULL	YES	NULL			
                    7	deleted_at	timestamp	NULL	NULL	YES	NULL			
                    8	created_at	timestamp	NULL	NULL	YES	NULL			
                    9	updated_at	timestamp	NULL	NULL	YES	NULL			

                */

                $companyId = 68; // for Zulikhbat Company
                $isActive = 'active'; // NOT SURE ???
                

                
                $branches = [
                    [
                        'id' => 1,  
                        'company_id' => $companyId,
                        'name' =>  ' عام',
                        'name_e' => 'General',               
                    ],

                ];

                foreach ($branches as $branch) {
                    Branch::create($branch);
                }    
    }
}
