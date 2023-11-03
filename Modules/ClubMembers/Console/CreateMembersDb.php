<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmTransaction;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateMembersDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'createmembe:db';

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
        $members = CmMember::whereHas('lastCmTransaction')->with('lastCmTransaction')->get();
        foreach ($members as $index => $member):

            $member->update([
                'last_transaction_date' => $member->lastCmTransaction['date'],
                'doc_date'              => $member->lastCmTransaction['date'],
                'last_transaction_id'   => $member->lastCmTransaction['id'],
            ]);

        endforeach;
        $this->info('Successfully Data Full Name In Table CmMember ');

//
//        $members = CmTransaction::get()->chunk(10000);
//        foreach ($members as $index => $member):
//            foreach ($member as $full_name):
//                $members =   CmMember::where('membership_number',$full_name->member_number)->first();
//                if ($members){
//                    $full_name->update(['cm_member_id' => $members->id]);
//
//                }
//            endforeach;
//            $this->info('Successfully Data Full Name In Table CmMember ');
//        endforeach;


//        $members = json_decode(file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/members.json')));
//        foreach ($members[2]->data  as  $member):
//            $updateMember = CmMember::find($member->MEMBER_ID);
//            if ($updateMember){
//
//                $updateMember->update([
//                    "membership_date"       => $member->ORDER_DATE == null ? null : \Carbon\Carbon::parse($member->ORDER_DATE)->format('Y-m-d'),
//                    "last_transaction_date" => $member->LstPayDate == null ? null :\Carbon\Carbon::parse($member->LstPayDate)->format('Y-m-d'),
//                    "last_transaction_id"   => $member->LstPayDoc,
//                    "doc_date"              => $member->DOC_DATE == null   ? null:\Carbon\Carbon::parse($member->DOC_DATE)->format('Y-m-d'),
//                    "doc_no"                => $member->DOC_NO,
//                ]);
//
//            }
//        endforeach;
        $this->info('Successfully Data Full Name In Table CmMember ');

//        CmMember::create([
//            "id"                => $member->MEMBER_ID,
//            "applying_number"   => $member->ORDER_NO,
//            "membership_number" => $member->MemberNo,
//            "acceptance"        => $member->ORDER_TYPE,
//            "home_address"      => $member->HouseAddress,
//            "membership_date"   => $member->ORDER_DATE == null ? null : \Carbon\Carbon::parse($member->ORDER_DATE)->format('Y-m-d'),
//            "national_id"       => $member->NationalNo,
//            "nationality_class" => $member->Cvlid,
//            "first_name"        => $member->FNAME,
//            "second_name"       => $member->SNAME,
//            "third_name"        => $member->TNAME,
//            "last_name"         => $member->FORNAME,
//            "family_name"       => $member->ZFAM_NAME,
//            "birth_date"        => $member->BIRTH_DATE,
//            "accepted"          => $member->ACCEPTED,
//            "sponsor_id"        => $member->Sponsor,
//            "degree"            => $member->StudyDegree,
//            "job"               => $member->employee,
//            "work_phone"        => $member->JobTel,
//            "work_address"      => $member->JobAddress,
//            "home_phone"        => $member->HouseTel,
//            "member_status_id"  => $member->ZSTATUS,
//            "last_transaction_date" => $member->LstPayDate == null ? null :\Carbon\Carbon::parse($member->LstPayDate)->format('Y-m-d'),
//            "last_transaction_id"   => $member->LstPayDoc,
//             "doc_date"        => $member->DOC_DATE == null   ? null:\Carbon\Carbon::parse($member->DOC_DATE)->format('Y-m-d'),
//            "doc_no"           => $member->DOC_NO,
//            "sponsership"      => $member->Sponsership,
//        ]);
    }


}

