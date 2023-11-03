<?php

namespace App\Console\Commands;

use App\Repositories\DocumentHeader\DocumentHeaderRepository;
use Illuminate\Console\Command;

class DailyInvoiceBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



    public function __construct(private DocumentHeaderRepository $documentHeader )
    {
        parent::__construct();
        $this->documentHeader = $documentHeader ;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->documentHeader->checkBooking();
        $this->info('Again command run successfully!');

    }
}
