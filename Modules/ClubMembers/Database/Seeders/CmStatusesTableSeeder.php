<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Status;
use Modules\ClubMembers\Entities\CmMember;



class CmStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $moduleType = "Modules\ClubMembers";

        $companyId = 68; //Zulikhbat Company


        $results = 
        [

                [
                    'name' => 'عليه اشتراكات',
                    'name_e' => 'Subscription Due',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],
    

                [
                    'name' => 'مسدد اشتراكات',
                    'name_e' => 'Subscription Paid',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],
                
                [
                    'name' => 'طلب العضوية',
                    'name_e' => 'Membership Requested',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,                    
                ],               

                [
                    'name' => 'لائحة الاعضاء',
                    'name_e' => 'On Members List',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],

                [
                    'name' => 'شطب بناءا علي طلبه',
                    'name_e' => 'Deleted due to Member Request',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,

                ],

                [
                    'name' => 'شطب لعدم السداد',
                    'name_e' => 'Deleted due to Non-payment',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,

                ],

                [
                    'name' => 'مسدد ليس له حق الحضور',
                    'name_e' => 'Subscription Paid, No Election Attentance',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,

                ],

                [
                    'name' => 'شطب للوفاة',
                    'name_e' => 'Deleted due to Death',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,

                ],

                [
                    'name' => 'تم استحداث البيانات',
                    'name_e' => 'Data Updating is Executed',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],

                [
                    'name' => 'شطب بموجب القرار الوزاري',
                    'name_e' => 'Deleted due to Ministerial Decision',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],

                [
                    'name' => 'مسدد اول مارس',
                    'name_e' => 'Subscription Paid On March',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ],

                [
                    'name' => 'شطب بموجب كتاب الهيئة',
                    'name_e' => ' Deleted due to Authoritarian',
                    'module_type' => $moduleType,
                    'company_id' => $companyId,
                ]

        ];

        foreach($results as $result){
            Status::create($result); 
        }  

    } // run

} // class

