<?php

namespace Modules\HR\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class MonthlyTransferDataFromRecentToArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        // which month to transfer?
        $year= now()->format('Y');
        $monthToBeTransferred = now()->format('m')-4;
        
       

        // Query the oldest month's data from the "recent" table
       $oldestData = 
       DB::table('hr_recent_attendance_departure')
       ->where('year', $year)
       ->where('month', $monthToBeTransferred)
       ->get();

       //return 100;
       //dd(100);
       
       if ($oldestData) {
           // Insert the data into the "archive" table
       /*
           foreach(collect($oldestData)->chunk(50) as $chunk)
           {
               DB::transaction(function () use($chunk) {
                   DB::table('hr_archive_attendance_departure')->insert($chunk->toArray());          
               }, 5);
           }
        */
          // return 222;


           // Delete the month-transferred data from the "recent" table to "archive" table
           DB::table('hr_recent_attendance_departure')
            ->where('year', $year)
            ->where('month', $monthToBeTransferred)->delete();
       }
    }
}
