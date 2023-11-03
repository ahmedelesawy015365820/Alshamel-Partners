<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MemberDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $sql = file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/members.sql'));

        // run file
        DB::unprepared($sql);

        $columns = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\GeneralImport(), base_path('Modules/ClubMembers/Resources/assets/db/members.xlsx'));
        $data = [];
        $keys = [];
        foreach ($columns as $column) {
            foreach ($column as $key =>  $row) {
                if ($key === array_key_first($column)) {
                    continue;
                }
                $row = [
                    "MEMBER_ID" => $row[0],
                    "ORDER_NO" => $row[1],
                    "MemberNo" => $row[2],
                    "ORDER_TYPE" => $row[3],
                    "HouseAddress" => $row[4],
                    "ORDER_DATE" => $this->fromExcelToLinux($row[5]),
                    "NationalNo" => $row[6],
                    "Cvlid" => $row[7],
                    "FNAME" => $row[8],
                    "SNAME" => $row[9],
                    "TNAME" => $row[10],
                    "FORNAME" => $row[11],
                    "ZFAM_NAME" => $row[12],
                    "BIRTH_DATE" => $this->fromExcelToLinux($row[13]),
                    "ACCEPTED" => $row[14],
                    "RESPONS" => $row[15],
                    "Sponsor" => $row[16],
                    "StudyDegree" => $row[17],
                    "employee" => $row[18],
                    "JobTel" => $row[19],
                    "JobAddress" => $row[20],
                    "HouseTel" => $row[21],
                    "ZSTATUS" => $row[22],
                    "LstPayDate" => $this->fromExcelToLinux($row[23]),
                    "LstPayDoc" => $row[24],
                    "DOC_DATE" => $this->fromExcelToLinux($row[25]),
                    "DOC_NO" => $row[26],
                    "MeetingDate" => $row[27],
                    "MeetingNumber" => $row[28],
                    "Sponsership" => $row[29]
                ];

                $data[] = $row;
            }
        }
        // insert data to cm_members
        foreach ($data as $single) {
            $this->info('seeded ' . $single['MEMBER_ID']);
            DB::table('Members')->insert($single);
        }
        $this->info('Inserting data to cm_members table');

        if (Schema::hasColumn('cm_members', 'applying_number')) {
            Schema::table('cm_members', function ($table) {
                $table->dropColumn("applying_number");
            });
        }
        // change gender to nullable in cm_members
        Schema::table('cm_members', function ($table) {
            $table->boolean("gender")->nullable()->change();
            $table->string("third_name")->nullable()->change();
            $table->string("last_name")->nullable()->change();
            $table->string("family_name")->nullable()->change();
            $table->integer("applying_number")->nullable();
        });

        $members = DB::table('Members')->get();
        $this->FinancialYears();
        foreach ($members as $member) {
            $new_member = \Modules\ClubMembers\Entities\CmMember::create([
                "id" => $member->MEMBER_ID,
                "applying_number" => $member->ORDER_NO,
                "membership_number" => $member->MemberNo,
                "home_address" => $member->HouseAddress,
                "membership_date" => $member->ORDER_DATE,
                "nationality_class" => $member->NationalNo,
                "national_id" => $member->Cvlid,
                "first_name" => $member->FNAME,
                "second_name" => $member->SNAME,
                "third_name" => $member->TNAME,
                "last_name" => (string) $member->FORNAME,
                "family_name" => $member->ZFAM_NAME,
                "birth_date" => $member->BIRTH_DATE,
                "acceptance" => $member->ACCEPTED,
                "degree" => $member->StudyDegree,
                "job" => $member->employee,
                "work_phone" => $member->JobTel,
                "work_address" => $member->JobAddress,
                "home_phone" => $member->HouseTel,
                "status_id" => $member->ZSTATUS + 1,
                "session_date" => $member->MeetingDate,
                "session_number" => $member->MeetingNumber,
                "financial_year_id" => date('2022-12-31') >= $member->ORDER_DATE ? 1 : 2,

            ]);
        }

        if ($member->Sponsor) {
            $sponsor = DB::table('Members')
                ->where('MEMBER_ID', $member->Sponsor)
                ->first();
            if ($sponsor) {
                $name = $sponsor->FNAME . ' ' . $sponsor->SNAME . ' ' . $sponsor->TNAME . ' ' . $sponsor->FORNAME;
                $new_sponsor = \Modules\ClubMembers\Entities\CmSponser::where('name', $name)->first();
                if (!$new_sponsor) {
                    $new_sponsor = \Modules\ClubMembers\Entities\CmSponser::create([
                        "name" => $name,
                        "name_e" => $name,
                    ]);
                }
                $new_member->update(['sponsor_id' => $new_sponsor->id]);
            } else {
                $new_member->update(['sponsor_id' => 1]);
            }
        }
        $this->paymentTransactions();

        // drop table  Members
        Schema::dropIfExists('Members');
        $this->info('Members table seeded!');
    }


    private function fromExcelToLinux($excel_time)
    {
        return date('Y-m-d', ($excel_time - 25569) * 86400);
    }


    private function FinancialYears()
    {
        //         1- 1977 , 31/12/2022
        // 2 1/1/2023 , 31/12/2023
        $data = [
            [
                'name' => "السنة الأولي",
                'name_e' =>  'first year',
                'start_date' => "1977-01-01",
                'end_date' => "2022-12-31",
            ],
            [
                'name' => "السنة الثانية",
                'name_e' =>  'second year',
                'start_date' => "2023-01-01",
                'end_date' => "2023-12-31",
            ],
        ];

        foreach ($data as $single) {
            \App\Models\FinancialYear::create($single);
        }
        $this->info('financial years seed');
    }


    private function paymentTransactions()
    {
        $this->info("start payment transaction");
        // get data from sql file
        $branch_id = \App\Models\Branch::create([
            "name_e" => "old branch",
            "name" => "فرع قديم",
        ]);
        $columns = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\GeneralImport(), base_path('Modules/ClubMembers/Resources/assets/db/PaymentTransactions.xlsx'));
        $data = [];

        foreach ($columns as $column) {
            foreach ($column as $key =>  $row) {
                if ($key === array_key_first($column)) {
                    continue;
                }
                $row = [
                    "MEMBER_ID" => $row[0],
                    "MemberNO" => $row[1],
                    "user_id" => $row[2],
                    "AMOUNT" => $row[3],
                    "DocNo" => $row[4],
                    "DocDate" => $this->fromExcelToLinux($row[5]),
                    "YersPay" => $row[6]
                ];

                $data[] = $row;
            }
        }
        foreach ($data as $payment) {
            $member = \Modules\ClubMembers\Entities\CmMember::where('id', $payment['MEMBER_ID'])->first();
            \Modules\ClubMembers\Entities\CmTransaction::create([
                'cm_member_id' => $payment['MEMBER_ID'],
                "member_number" =>  $payment['MemberNO'],
                "amount" =>  $payment['AMOUNT'],
                "old_doc" => $payment['DocNo'],
                "date" => $payment['DocDate'],
                "year_from" => $payment['YersPay'],
                "year_to" => $payment['YersPay'],
                "document_id" => 8,
                "branch_id" => $branch_id->id,
                "serial_id" => date('2022-12-31') >= $member->ORDER_DATE ? 1 : 2,
                'serial_number' => date('2022-12-31') >= $member->ORDER_DATE ? $member->id
                    : 8,
                'prefix' => date('2022-12-31') >= $member->ORDER_DATE ? 'old-one' : 'cast-2023',
                "type" => "subscribe"
            ]);
            $this->info('seeded ' . $payment['MemberNO']);
            $this->info("finish payment transaction");
        }
    }
}
