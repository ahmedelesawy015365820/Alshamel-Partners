<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SerialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_serials')->delete();

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
            10	created_at	timestamp	NULL	NULL	YES	NULL			
            11	updated_at	timestamp	NULL	NULL	YES	NULL			
            12	name	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			
            13	name_e	varchar(191)	utf8mb4	utf8mb4_unicode_ci	YES	NULL			


        */

        $companyId = 68; // for Zulikhbat Company
        $documentId = 8; // Subscription
        $branchId = 1; // GENERAL
        $suffix = '';
        $restartPeriod = 3; // Open

        $startNumber = 1; // NOT SURE???      




        $admins = [
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

            ],
            [
                'id' => 2,
                'name' => 'Islam Khaled',
                'email' => 'islam.khaled13a@gmail.com',
                'password' => bcrypt('123456789'),
                'phone' => '01015949894',
                'role_id' => null,
            ],

        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
