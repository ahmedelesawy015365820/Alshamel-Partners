<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MemberTypeIdDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'membertypeid:db';

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
        $membersTests    =   DB::table('cm_members_test')->get();
        foreach ($membersTests as  $membersTests){
            $CmMembes = CmMember::find($membersTests->id);
            if ($CmMembes){
                $CmMembes->update(['member_type_id'=> $membersTests->member_type_id]);
            }
        }
        $this->info('Successfully Data Full Name In Table CmMember ');
    }


}
