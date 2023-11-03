<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\EmployeesTimeTablesHeader;
use Modules\HR\Entities\TimeTablesDetail;
use Modules\HR\Entities\TimeTablesHeader;
use Modules\HR\Entities\TimeTableType;
use Modules\HR\Transformers\AttendanceDepartureReportResource;
use Modules\HR\Entities\Fingerprint;
use Modules\HR\Entities\HrAttendanceDepartureReport;
use Modules\HR\Entities\AttendanceSetting;
use Illuminate\Support\Facades\DB;
use DateTime;
use \Illuminate\Support\Collection;
use Modules\HR\Jobs\MonthlyTransferDataFromRecentToArchive;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Str;


class ReportController extends Controller
{
    public function __construct(private Employee $model, private EmployeesTimeTablesDetail $employeesTimeTableDetailModel, private EmployeesTimeTablesHeader $employeesTimeTablesHeaderModel)
    {
        $this->model = $model;
        $this->employeesTimeTableDetailModel = $employeesTimeTableDetailModel;
        $this->employeesTimeTablesHeaderModel = $employeesTimeTablesHeaderModel;
    }


    public function AttendanceDeparture(Request $request)
    {
        
        /*
        $path = app_path('Modules') . DIRECTORY_SEPARATOR . request()->segment(2); //$request->path();
        $firstSegment = Str::after($path, 'Modules/');

        return Module::find($firstSegment)->getName(); 
        // $currentPath = 
        return app_path('Modules') . DIRECTORY_SEPARATOR . request()->segment(2); 
        // Assuming module names are in the second segment
        
        return $request->path()->segment(2);
        return Module::find($request->path())->getName();
        */

        
        // comment before push
        //$request->employeesIds = [5,6]; // Rana

        if($request->employeesIds)
        {
            $employeesIds = $request->employeesIds;

        }else{
            return 'Please choose employees for report.';
        }

        if ($request->month && $request->year) {
           
            $year = $request->year;
            $month = $request->month;
            $date_end = Carbon::create($year, $month)->lastOfMonth();
            $startOfMonth = Carbon::create($year, $month, 1);
            $endOfMonth = Carbon::create($year, $month, $date_end->day);
            $monthInterval = CarbonPeriod::create($startOfMonth, $endOfMonth);

            $monthInterval_2 = CarbonPeriod::create($startOfMonth, $endOfMonth)->toArray();

            $formattedMonthInterval = array_map(function ($monthInterval_2) {
                return $monthInterval_2->format('Y-m-d');
            }, $monthInterval_2);

            //return sizeOf($monthInterval);
            
            
        }else{
            return 'Please choose month and year.';
        }
    
        $reportData = [];
        $row = 0;


        $attendanceSetting = AttendanceSetting::first();

        // case NO records in attendance setting table
        if(!$attendanceSetting)
            return 'Please insert attendance setting';

        $minsPreIn = $attendanceSetting['pre_in'];
        $minsPostIn = $attendanceSetting['post_in'];
        $minsAbsentIn = $attendanceSetting['absent_minutes'];
        $minsPreOut = $attendanceSetting['pre_out'];
        $minsPostOut = $attendanceSetting['post_out'];
        $minsMaxOut = $attendanceSetting['max_out'];

        $employeesMissingDays = [];

        // if there is NO newReport signal
        if(!$request->new_report)
        {
                // Getting the records missing for each employee in coming request
                $availableTableRecords = 
                DB::table('hr_recent_attendance_departure')
                ->where('year', $year)
                ->where('month', $month)
                ->get();

                //return $availableTableRecords;

                

                $i = 0;
                foreach ($employeesIds as $employeeId) {
                
                        $availableDays 
                        = $availableTableRecords
                        ->where('employee_id', $employeeId)
                        ->pluck('day')
                        ->toArray();

                        //return $availableDays;

                        $missingDays = array_diff($formattedMonthInterval, $availableDays);

                        //return array_values($missingDays);

                        $employeesMissingDays[$employeeId] = array_values($missingDays);
                        //$i++;
                }
                //return in_array('2023-10-15',$employeesMissingDays[5]); -- 1

                //return 100;
        }

        //return $employeesMissingDays;

        


        foreach ($employeesIds as $employee_id) {

            $selectedEmployee = $this->model->find($employee_id, ['name', 'att_code']);

            if(empty($selectedEmployee))
                continue;
            else{
                $att_code = $selectedEmployee['att_code']; 
                $employee_name = $selectedEmployee['name']; 
            }

            // case NO new report signal            
            // case we have all the employee data for this month, year
            if(!$request->new_report)
            if(sizeOf($employeesMissingDays) == 0 || sizeOf($employeesMissingDays[$employee_id]) == 0)   
                continue;

            //return 150;
            
            
            // return [$att_code, $employee_name]; // 5, Rana
           
            $employees_timetables_header_ids
            = $this->employeesTimeTableDetailModel
            ->where('employee_id', $employee_id) 
            ->pluck('employees_timetables_header_id'); 

            // return $employees_timetables_header_ids; // 1, 4 -- ok
            
            if(!$employees_timetables_header_ids) 
                continue;
            
            foreach ($monthInterval as $date_i) 
            {
                // case NO new report signal            
                // case the current day in loop is NOT in the missing data (i.e. it exists in db)
                    
                    if(!$request->new_report)
                    {
                        $not_in_array = !in_array($date_i->format('Y-m-d'),$employeesMissingDays[$employee_id]);

                        if($not_in_array == 1)
                            continue;
                    }
                    
                
                        
                        
                    //return $date_i->format('Y-m-d');
                    // initializing the row
                    $reportData[$row]['month'] = $month;
                    $reportData [$row]['year'] = $year;
                    $reportData [$row]['employee_id'] = $employee_id;
                    $reportData [$row]['name'] = $employee_name;
                    $reportData [$row]['day'] = $date_i->format('Y-m-d');
                    $reportData [$row]['attend_1'] = null;
                    $reportData [$row]['depart_1'] = null;
                    $reportData [$row]['attend_2'] = null;
                    $reportData [$row]['depart_2'] = null;
                    $reportData [$row]['late'] = null;
                    $reportData [$row]['overtime'] = null;
                    $reportData [$row]['exact_hours'] = null;
                    $reportData [$row]['absence'] = "absent"; // "attended"(default), "absent 1", "absent 2", "absent", 
                    $reportData [$row]['notes'] = null;

                    

                    // based on current day and employee header ids, get timetable header ids
                    $employees_timetables_header = 
                    EmployeesTimetablesHeader::whereIn('id', $employees_timetables_header_ids)
                    ->where('start_from', '<=', $date_i->format('Y-m-d')) 
                    // until here it gives (17(id=1), 18(id=4)) with same start_from
                    ->orderBy('start_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                     // 18

                     //return $employees_timetables_header;

                    // No VALID day detail -- 1 (no corresponding timetable header id for that employee on that day)
                    // skip to next row
                    if(!$employees_timetables_header) 
                    {
                        $reportData [$row]['notes'] = "ليس له دوام";
                        $reportData[$row]['absence'] = null;
                        $row++;

                        continue;
                    }    

                    $employees_timetables_header_id = $employees_timetables_header['timetables_header_id'];


                    //return $employees_timetables_header_id;
                   

                    // getting shift type (1: fixed time, one shift, 2: fixed time, two shifts)
                    $timetableHeader = TimeTablesHeader::where('id', $employees_timetables_header_id)->first();

                    // No VALID day detail -- 2 (no corresponding shift-type in TimeTableHeader)
                    // skip to next row
                    if(!$timetableHeader) 
                    {
                        $reportData [$row]['notes'] = "ليس له دوام";
                        $reportData[$row]['absence'] = null;
                        $row++;

                        continue;
                    }    

                    $timetable_types_id = $timetableHeader['timetable_types_id'];

                
                    //return $timetable_types_id;

                    $shiftTypeId = TimeTableType::where('id', $timetable_types_id)->first();

                    //return $shiftTypeId;

                    // No VALID day detail -- 3 (No corresponding shift-type in TimeTableType)
                    // skip to next row
                    if(!$shiftTypeId) 
                    {
                        $reportData [$row]['notes'] = "ليس له دوام";
                        $reportData[$row]['absence'] = null;
                        $row++;

                        continue;
                    }    
                    
                    $shiftTypeId = $shiftTypeId['id'];
                    //return $shiftTypeId; // 2 (2 shifts)
                    
                    
                    // day as number
                    $currentDateDayNo = Carbon::parse($date_i)->dayOfWeekIso; // Monday: 1, Sunday: 7

                    // get day detail where the shifts exact start and end times are set
                    $dayDetail =
                    TimeTablesDetail::where('timetables_header_id', $employees_timetables_header_id)
                    ->where('day_no', $currentDateDayNo)
                    ->first(); // id: 26, day_no: 1

                    //return $dayDetail;

                    // case no day detail, skip to next row
                    if(!$dayDetail) 
                    {
                        $reportData [$row]['notes'] = "ليس له دوام";
                        $reportData[$row]['absence'] = null;
                        $row++;
                        continue;
                    }    

                    //return $dayDetail;

                    // before getting all the details let us check first if the day in the day detail is OFF 
                    $is_off = $dayDetail['is_off'];

                    //return $is_off; // 0
                   
                    // case day is OFF
                    // skip to next row
                    if($is_off == 1){
                        $reportData [$row]['notes'] = "عطلة اسبوعية";
                        $reportData[$row]['absence'] = null;
                        
                        $row++;

                        continue; // go next day
                    }
                    

                    // get all fingerprints by this employee on that day
                    $dayFingerprints = Fingerprint::where('att_code', $att_code) 
                    ->where(DB::RAW('DATE_FORMAT(vdate, "%Y-%m-%d")'), $date_i->format('Y-m-d'))
                    ->get(); 
                    
                    // skip to next row
                    if(sizeOf($dayFingerprints) == 0) 
                    {
                        $row++;
                        continue;
                    }
                    
                    
                    //return $dayFingerprints; // ids = (3, 4, 9, 10) -- ok
                    
                    $timeFingerprints = collect();

                    $dayFingerprints->transform(function($item) use ($timeFingerprints) {
                        
                        // Extract the time from the date time value.
                        $time = Carbon::parse($item['vdate'])->toTimeString();

                        // Create a new item with the modified date time value and the original columns.
                        $modifiedItem = [
                            'id' => $item['id'],
                            'att_code' => $item['att_code'],
                            'vdate' => $time,
                            'att_type' => $item['att_type'],
                            'machine_id' => $item['machine_id'],
                            'created_at' => $item['created_at'],
                            'updated_at' => $item['updated_at']
                        ];

                        // Add the modified item to the new Laravel collection variable.
                        $timeFingerprints->add($modifiedItem);
                    });

                    

                    //return $reportData;



                    // return $dayDetail;
                                        
                        

                
                    // if we reach here it is NOT weekend
                    // getting day details

                    $shift1_from =  $dayDetail['shift1_from']; // 08:00:00
                    $timePreIn_1 = Carbon::parse($shift1_from)->subMinutes($minsPreIn)->format('H:i:s'); // 07:30:00
                    $timePostIn_1 = Carbon::parse($shift1_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
                    $timeAbsentIn_1 = Carbon::parse($shift1_from)->addMinutes($minsAbsentIn)->format('H:i:s'); // 09:00:00

                    //return [$shift1_from, $timePreIn_1, $timePostIn_1, $timeAbsentIn_1];

                    $shift1_to =  $dayDetail['shift1_to']; // 17:00:00
                    $timePreOut_1 = Carbon::parse($shift1_to)->subMinutes($minsPreOut)->format('H:i:s'); // 17:00:00
                    $timePostOut_1 = Carbon::parse($shift1_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00
                    $timeMaxOut_1 = Carbon::parse($shift1_to)->addMinutes($minsMaxOut)->format('H:i:s'); // 19:00:00


                    $shift2_from =  $dayDetail['shift2_from'];
                    $timePreIn_2 = Carbon::parse($shift2_from)->subMinutes($minsPreIn)->format('H:i:s');
                    $timePostIn_2 = Carbon::parse($shift2_from)->addMinutes($minsPostIn)->format('H:i:s');
                    $timeAbsentIn_2 = Carbon::parse($shift2_from)->addMinutes($minsAbsentIn)->format('H:i:s');

                    $shift2_to =  $dayDetail['shift2_to'];
                    $timePreOut_2 = Carbon::parse($shift2_to)->subMinutes($minsPreOut)->format('H:i:s');
                    $timePostOut_2 = Carbon::parse($shift2_to)->addMinutes($minsPostOut)->format('H:i:s');
                    $timeMaxOut_2 = Carbon::parse($shift2_to)->addMinutes($minsMaxOut)->format('H:i:s');
                    

                    // loop fingerprints until get first VALID fingerprint
                    // record fingerprint even if the person misses other fingerprints
                    

                    // getting VALID 1st shift fingerprints
                    //if($shiftTypeId == 1 || $shiftTypeId == 2)
                    //{
                        // shift 1 attend
                        $attendDetail_shift1
                        = 
                        $timeFingerprints
                        ->where('att_type', 0)
                        ->where('vdate', '>=', $timePreIn_1) // 07:30
                        ->where('vdate', '<=', $timeAbsentIn_1) // 09:00
                        ->sortBy('vdate') // earliest
                        ->first();
                        
                        if($attendDetail_shift1)
                            $reportData [$row]['attend_1'] = $attendDetail_shift1['vdate'];

                        
                        //return $reportData [$row]['attend_1']; //08:00:00
                        
                        //return [$timePreOut_1, $timeMaxOut_1];

                        // shift 1 depart
                        $departDetail_shift1 
                        = 
                        $timeFingerprints
                        ->where('att_type', 1)
                        ->where('vdate', '>=', $timePreOut_1)
                        ->where('vdate', '<=', $timeMaxOut_1)
                        ->sortBy('vdate') // earliest
                        ->first();

                        if($departDetail_shift1)
                            $reportData [$row]['depart_1'] = $departDetail_shift1['vdate'];

                        //return $reportData [$row]['depart_1']; //12:00:00
                        
                    //}

                    //return $reportData;

                    // getting VALID 2nd shift fingerprints
                    if($shiftTypeId == 2)
                    {
                        // shift 2 attend
                        $attendDetail_shift2 
                        = 
                        $timeFingerprints
                        ->where('att_type', 0)
                        ->where('vdate', '>=', $timePreIn_2)
                        ->where('vdate', '<=', $timeAbsentIn_2)
                        ->sortBy('vdate') // earliest
                        ->first();
                        
                        if($attendDetail_shift2)
                            $reportData [$row]['attend_2'] = $attendDetail_shift2['vdate'];
                        
                        // return $reportData [$row]['attend_2']; //14:30:00


                        // shift 2 depart
                        $departDetail_shift2
                        = 
                        $timeFingerprints
                        ->where('att_type', 1)
                        ->where('vdate', '>=', $timePreOut_2)
                        ->where('vdate', '<=', $timeMaxOut_2)
                        ->sortBy('vdate') // earliest
                        ->first();

                        if($departDetail_shift2)
                            $reportData [$row]['depart_2'] = $departDetail_shift2['vdate'];
                        
                        // return $reportData [$row]['depart_2']; // 17:44:59
                        
                    }

                // check absence status: "absent 1", "absent 2", "absent", "attended" (already default)
                // looking for absence cases only

                // case shift-type: 1-shift
                    
                    $exist_shift1 = 0;                    

                    if(($reportData [$row]['attend_1'] != null && $reportData [$row]['depart_1']) != null)
                    {
                        $reportData [$row]['absence'] = "attended";
                        $exist_shift1 = 1;
                    }      
                      
                    //return $exist_shift1;
                    
                    
                // case shift-type: 2-shift
                    
                    if($shiftTypeId == 2)
                    {
                        
                        $exist_shift2 = 0;
        
                        if( ($reportData [$row]['attend_2'] != null && $reportData [$row]['depart_2']) != null)
                            $exist_shift2 = 1;
                        
                        //return $exist_shift1;
                        
                        // no shift 1, yes shift 2
                        if(!$exist_shift1 && $exist_shift2)
                            $reportData [$row]['absence'] = "absent 1";
                       
                        // yes shift 1, no shift 2
                        else if($exist_shift1 && !$exist_shift2)
                            $reportData [$row]['absence'] = "absent 2";

                        // no shift 1, no shift 2
                        else if(!$exist_shift1 && !$exist_shift2)
                            $reportData [$row]['absence'] = "absent";

                        else $reportData [$row]['absence'] = "attended";
                            
                    }


                    // calculating TOTAL late time, over time, AND exact working time
                    // only if he/she attended ALL their shifts
                    
                    $late_shift1 = 0;
                    $overTime_shift1 = 0;
                    $exactHours_shift1 = 0;

                    $late_shift2 = 0;
                    $overTime_shift2 = 0;
                    $exactHours_shift2 = 0;
                    
                    if($reportData [$row]['absence'] == "attended"){

                        // 1st shift for both shift types

                        //return $reportData [$row]['attend_1'];
                        // late time is calculated after 08:15 (shift @ 08:00)
                        if(
                            $reportData [$row]['attend_1'] != null &&
                            $reportData [$row]['attend_1'] > $timePostIn_1 
                        )
                            $late_shift1 = 
                            Carbon::parse($reportData [$row]['attend_1'])->diffInMinutes(Carbon::parse($shift1_from)); 
                            
                            //return $late_shift1;
                        
                        // over time is calculated after 17:30 (shift @ 17:00)
                        if(
                            $reportData [$row]['depart_1'] != null &&
                            $reportData [$row]['depart_1'] > $timePostOut_1 
                        )
                            $overTime_shift1 = Carbon::parse($reportData [$row]['depart_1'])->diffInMinutes(Carbon::parse($shift1_to)); 

                            //return $overTime_shift1;
                        // exact working time is calculated via attend time and depart time
                        if($reportData [$row]['attend_1'] != null && $reportData [$row]['depart_1'] != null)
                            $exactHours_shift1 = Carbon::parse($reportData [$row]['depart_1'])
                            ->diffInMinutes(Carbon::parse($reportData [$row]['attend_1'])); 



                        if($shiftTypeId == 2) // case two-shifts
                        {    
                            // late time is calculated after 12:15 (shift @ 12:00)
                            if(
                                $reportData [$row]['attend_2'] != null &&
                                $reportData [$row]['attend_2'] > $timePostIn_2 
                            )
                                $late_shift2 = 
                                Carbon::parse($reportData [$row]['attend_2'])->diffInMinutes(Carbon::parse($shift2_from));   
                            
                            // over time is calculated after 17:30 (shift @ 17:00)
                            if(
                                $reportData [$row]['depart_2'] != null &&
                                $reportData [$row]['depart_2'] > $timePostOut_2 
                            )
                                $overTime_shift2 = Carbon::parse($reportData [$row]['depart_2'])->diffInMinutes(Carbon::parse($shift2_to)); 
    
                            // exact working time is calculated via attend time and depart time
                            if($reportData [$row]['attend_2'] != null && $reportData [$row]['depart_2'] != null)
                                $exactHours_shift2 = Carbon::parse($reportData [$row]['depart_2'])
                                ->diffInMinutes(Carbon::parse($reportData [$row]['attend_2'])); 
    
    
                        } // case two-shifts


                    }
        

                    // Sum two-shifts late
        
                            $reportData [$row]['late'] = $late_shift1+$late_shift2; // in mins
                            
                    // Sum two-shifts overtime
    
                            $reportData [$row]['overtime'] = $overTime_shift1+$overTime_shift2;
    
    
                    // Sum exact hours for two shifts
    
                            $reportData [$row]['exact_hours'] = $exactHours_shift1+$exactHours_shift2;
          
                    
                    $row++;    


                                    
                
                //return $reportData;
               
            } // looping over days in interval for each employee
            

        } // looping over employees_ids

        //return $reportData; // only missing from db

        (new MonthlyTransferDataFromRecentToArchive())->dispatch();
        //return 333;

        // case new report request, delete
        if($request->new_report)
             HrAttendanceDepartureReport::where('year', $year)
            ->where('month', $month)->whereIn('employee_id', $employeesIds)->delete();

        //return 100;
        
        // inserting into the db
        foreach(collect($reportData)->chunk(50) as $chunk)
        {
            DB::transaction(function () use($chunk) {
                DB::table('hr_recent_attendance_departure')->insert($chunk->toArray());          
            }, 5);
        }
        
        //return 100;

        // getting report result from db
        //$reportDataFromDb =
        $models=
        HrAttendanceDepartureReport::where('year', $year)
        ->where('month', $month)
        ->whereIn('employee_id', $employeesIds)
        ->where('day', '>=',$startOfMonth)
        ->where('day', '<=', $endOfMonth);
        //->get();

        //foreach($models->chunk(5) as $chunk)
        //    return $chunk;

        //return $models;

        //return $reportDataFromDb;



        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AttendanceDepartureReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
        

        /*
        $perPage = $request->per_page ?? null;
        $page = $request->page ?? null;

        if ($perPage || $page) {
            $reportDataChunk = array_slice($reportData, ($page - 1) * $perPage, $perPage);
        } else {
            $reportDataChunk = $reportData;
        }
        
        return [
            "message" => "success",
            "data" => $reportDataChunk,
            "pagination" => $perPage || $page ? [
                "per_page" => $perPage,
                "path" => $request->url(),
                "total" => count($reportData),
                "current_page" => $page,
                "next_page_url" => $page < ceil(count($reportData) / $perPage) ? $request->url() . "?page=" . ($page + 1) : null,
                "previous_page_url" => $page > 1 ? $request->url() . "?page=" . ($page - 1) : null,
                "last_page" => ceil(count($reportData) / $perPage),
                "has_more_pages" => $page < ceil(count($reportData) / $perPage),
                "from" => (($page - 1) * $perPage) + 1,
                "to" => min(count($reportData), $page * $perPage)
            ] : null
        ];
        */
    } // AttendanceDeparture function

    /*
    public function Shift($dayDetail, $attendenceSetting, $employeeFingerprints)
    {
        // inputs: 
        // (1) shift criteria in day detail
        // (2) attendance settings
        // (3) employee fingerprints on that day
        // (4) shift type
        
        // outputs:
        // (1) valid fingerprints
        // (2) attended this shift or not
        // (3) late time of this shift
        // (4) over time of this shift

        
        $timeFingerprints = collect();

        $dayFingerprints->transform(function($item) use ($timeFingerprints) {
            
            // Extract the time from the date time value.
            $time = Carbon::parse($item['vdate'])->toTimeString();

            // Create a new item with the modified date time value and the original columns.
            $modifiedItem = [
                'id' => $item['id'],
                'att_code' => $item['att_code'],
                'vdate' => $time,
                'att_type' => $item['att_type'],
                'machine_id' => $item['machine_id'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at']
            ];

            // Add the modified item to the new Laravel collection variable.
            $timeFingerprints->add($modifiedItem);
        });




    }
   
   
    public function WorkingDay()
    {

    }
    */

} // class ReportController
