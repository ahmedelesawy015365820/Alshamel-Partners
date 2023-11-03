<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Serial;



class CmSerialTableSeeder extends Seeder
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
            2	start_no	bigint(20)	NULL	NULL	YES	NULL			Op code (1=Add,2= Update, 3=Delete 4=View, ….)
            3	perfix	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
            4	suffix	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
            5	restart_period	varchar(191)	utf8mb4	utf8mb4_unicode_ci	NO	0			Daily, monthly, yearly, open
            6	company_id	bigint(20) unsigned	NULL	NULL	YES	NULL			
            7	document_id	bigint(20) unsigned	NULL	NULL	YES	NULL			
            8	branch_id	bigint(20) unsigned	NULL	NULL	YES	NULL			
            9	is_default	varchar(191)	utf8mb4	utf8mb4_unicode_ci	NO	0			1=Yes, 0=No
            12	name	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
            13	name_e	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			

        */

        $companyId = 68; // for Zulikhbat Company
        $documentId = 8; // Subscription
        $branchId = 1; // GENERAL
        $suffix = '';
        $restartPeriod = 365; // days

        $startNumber = 1; // checked !!     




        $serials = [
            [
                'id' => 1,
                
                'perfix' => 'OLD',
                'name' => 'قديم',
                'name_e' => 'Old Data',

                'company_id' => $companyId,
                'document_id' => $documentId,
                'branch_id' => $branchId, 
                'suffix' => $suffix,

                'restart_period' => $restartPeriod, 
                'start_no' => $startNumber,
            ],
            [
                'id' => 2,
                
                'perfix' => 'RNW_MEN',
                'name' => 'تجديد عضوية للرجال',
                'name_e' => 'Membership Renewal for Men',

                'company_id' => $companyId,
                'document_id' => $documentId,
                'branch_id' => $branchId, 
                'suffix' => $suffix,

                'restart_period' => $restartPeriod, 
                'start_no' => $startNumber,
            ],

            [
                'id' => 3,
                
                'perfix' => 'RNW_WMN',
                'name' => 'تجديد عضوية للنساء',
                'name_e' => '’Membership Renewal for Women',

                'company_id' => $companyId,
                'document_id' => $documentId,
                'branch_id' => $branchId, 
                'suffix' => $suffix,

                'restart_period' => $restartPeriod, 
                'start_no' => $startNumber,
            ],

            [
                'id' => 4,
                
                'perfix' => 'NEW_MEN',
                'name' => 'عضوية جديده للرجال',
                'name_e' => 'New Membership for Men',

                'company_id' => $companyId,
                'document_id' => $documentId,
                'branch_id' => $branchId, 
                'suffix' => $suffix,

                'restart_period' => $restartPeriod, 
                'start_no' => $startNumber,
            ],
            [
                'id' => 5,
                
                'perfix' => 'NEW_WMN',
                'name' => 'عضوية جديده للنساء',
                'name_e' => 'New Membership for Women',

                'company_id' => $companyId,
                'document_id' => $documentId,
                'branch_id' => $branchId, 
                'suffix' => $suffix,

                'restart_period' => $restartPeriod, 
                'start_no' => $startNumber,
            ],

            


        ];

        foreach ($serials as $serial) {
            Serial::create($serial);
        }
    }
}
