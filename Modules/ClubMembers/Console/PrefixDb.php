<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\ClubMembers\Entities\CmTransaction;
use App\Models\Serial;

class PrefixDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'Prefix:db';

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
        $serial =Serial::where('id', 1)->value('perfix');
        

        $i = 1;
        
        $transactions =CmTransaction::all();

        foreach ($transactions as $transaction) {
            
            $documentNo = CmTransaction::where('id', $transaction->id)->value('document_id');
            
            $prefix = $serial . '-' . $transaction->document_id . '-' . $i;

            CmTransaction::where('id', $transaction->id)->update(['prefix' => $prefix]);
            
            $i++;
        }

        $this->info('Prefix Column is Seeded');
    }

   }
