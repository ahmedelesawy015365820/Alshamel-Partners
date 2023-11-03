<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Modules\ClubMembers\Entities\CmMember;


class FullNameDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fullname:db';

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
        $names = [];
        $members =  CmMember::get()->chunk(1000);
        foreach ($members as  $member):
            foreach ($member as   $full_name ):
                $names = [];
                if ($full_name->first_name != null){
                    $names['first_name']  = $full_name->first_name;
                }
                if ($full_name->second_name != null){
                    $names['second_name'] = $full_name->second_name;
                }
                if ($full_name->third_name != null){
                    $names['third_name']  = $full_name->third_name;
                }
                if ($full_name->last_name != null){
                    $names['last_name']  = $full_name->last_name;
                }
                if ($full_name->family_name != null){
                    $names['family_name']  = $full_name->family_name;
                }

                   $array = implode(' ', $names);


                $full_name->update(['full_name'=>$array]);
            endforeach;
        endforeach;
        $this->info('Successfully Data Full Name In Table CmMember ');
    }

   }
