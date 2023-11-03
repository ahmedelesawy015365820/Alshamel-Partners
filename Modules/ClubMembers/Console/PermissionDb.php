<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\FinancialYear;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmTypePermission;

class PermissionDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'permission:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {



        ini_set('max_execution_time', 3600); // 3600 seconds = 60 minutes
        set_time_limit(3600);
        ini_set('memory_limit', -1);


        $financialyear = FinancialYear::where('is_active', 1)->first();
        if ($financialyear){

            //reset the financial status id in Cm Members for all active (saaary) members to 2 (غير مسدد)
            // reset the member permission id in Cm Members for all active (saary) members to 1 (ليس له حق الحضور)
            /// update to columns  financial_status_id =  غير مسدد and  members_permissions_id = ليس له حق الحضور  in table CmMembers
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 2])->update([
                'financial_status_id' => 1,  // غير مطلوب السداد
                'members_permissions_id' => 4 // كل الحقوق
            ]);

            // عضو عادي
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 1])->update([
                'financial_status_id' => 2,  // غير مسدد
                'members_permissions_id' => 1 // ليس له حق
            ]);


            // all settings
            $settings = DB::table('cm_type_permissions')->where('cm_permissions_id', '>', 1)->orderBy('cm_permissions_id', 'asc')->get();

            // get the active financial year

            // filter the "saaaaary" members which have at least one last_transaction_date
            // that its last_transaction_year as the financial year
            $running_member_all = CmMember::where('member_status_id', 1)	// ساري
                ->where(function ($q) use ($financialyear) {
                    $q->whereNotNull('last_transaction_date')
                        ->where('last_transaction_year', $financialyear->year) ;// int ? int
                })->orWhere('member_kind_id',2)->get();

            // allowed_vote_date
            $date_allowed_vote_date = '2023-02-28';


            $update_3ady_members = CmMember::where('member_status_id', 1)	// ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '<=', $date_allowed_vote_date)
                ->update
                ([
                    'financial_status_id'    => 3,  //مسدد في الموعد
                ]);

            $update_3ady_members_2 = CmMember::where('member_status_id', 1)	// ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '>', $date_allowed_vote_date)
                ->update
                ([
                    'financial_status_id'    => 4,  //مسدد في الموعد
                ])
            ;

            foreach ($running_member_all as  $member){
                $member->refresh();

                $dbDate = \Carbon\Carbon::parse($member->membership_date)->format('Y-m-d');
                $diffYears = \Carbon\Carbon::now()->diffInYears($dbDate);


                foreach ($settings as $setting){

                    if($member->member_kind_id  == $setting->cm_members_type_id && $setting->cm_financial_status_id == $member->financial_status_id && $diffYears >= $setting->membership_period)
                    {

                        $member->update
                        ([
                            'members_permissions_id' => $setting->cm_permissions_id,
                        ]);
                        //   }

                        break; // exit the for each on the permissions => he/she can NOT achieve better
                        //}

                    }else{
                        $member->update
                        ([
                            'members_permissions_id' => 1,
                        ]);

                    }


                }

            }


        }else{

            $this->info('There must be an active financial year!');

        }

        $this->info('Successfully Data  In Table CmMember ');

    }


}
