<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Repositories\Message\MessageInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\AttendanceSetting;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\EmployeesTimetablesHeader;
use Modules\HR\Entities\TimeTablesHeader;
use Modules\HR\Entities\TimeTablesDetail;
use Modules\HR\Entities\TimeTableType;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Nwidart\Modules\Facades\Module;
use App\Models\MessageReceiverContact;
use App\Models\GeneralMessageRequest;
use App\Models\Employee;
use App\Traits\sendMessage;
use Modules\HR\Entities\Fingerprint;

class MessageController extends Controller
{
    use sendMessage;

    public function __construct(private MessageInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new MessageResource($model));
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', MessageResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(MessageRequest $request)
    {

        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(MessageRequest $request, $id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->have_children) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }
    
//     public function sendMessage(Request $request)
//     {
//         //try {
             
//             //return $request->fingerprints;

//             // ===================
//             // comment before push
//             $request->fingerprints = DB::table('hr_fingerprint')->whereIn('id', [1, 3])->get();


//             // UNcomment before push
//            if(!$request->fingerprints)
//                 return 'please send fingerprints to process';

//             // ===================

//             $receivedFingerprints = $request->fingerprints;

//             //return $receivedFingerprints;

            

//             // get THE ONLY ONE ROW of attendance setting
//             // sth changed about attendance setting => change HERE!!
//             $attendanceSetting = AttendanceSetting::first();

//             // case NO records in attendance setting table
//             if(!$attendanceSetting)
//                 return 'Please insert attendance setting';

//             //return $attendanceSetting;

//             // mins around shift times for late, overtime and absent statuses
//             $minsPreIn = $attendanceSetting['pre_in'];
//             $minsPostIn = $attendanceSetting['post_in'];
//             $minsAbsentIn = $attendanceSetting['absent_minutes'];
//             $minsPreOut = $attendanceSetting['pre_out'];
//             $minsPostOut = $attendanceSetting['post_out'];
//             $minsMaxOut = $attendanceSetting['max_out'];
//             $messageToEmployee = $attendanceSetting['message_to_employee'];


    

            
//             // collection carrying msgs (if exist) for each employee having fingerprint in the request array
//             $fingerprintMessages = [];
//             $messageContacts = [];
            
//             // handle_type ([H]andle, [D]elay, [O]vertime), overtime/delay in mins

//             $row = 0;

//             // looping over received fingerprints
//             foreach ($receivedFingerprints as $fingerprint) {

//                 //return $fingerprint;
//                 // case NULL attendance code => skip
//                     if(!$fingerprint)
//                         continue;
                        

//                     // getting att_code of fingerprint
//                     $att_code = $fingerprint->att_code;

                    

//                     $employee_id = DB::table('general_employees')->where('att_code', $att_code)->value('id');                    

//                     // case NULL employee_id
//                     if(!$employee_id)
//                         return "no equivalent employee_id for the specified att_code";
                    
//                     //return $employee_id;

//                     // using employee_id to get $employees_timetables_header_ids
//                     // note: it can be more than one!! => array maybe given
//                     $employees_timetables_header_ids
//                     = EmployeesTimeTablesDetail::where('employee_id', $employee_id) 
//                     ->pluck('employees_timetables_header_id'); 
        
//                     // case NULL employees_timetables_header_ids
//                     if(!$employees_timetables_header_ids) 
//                         continue;
                    

//                     //return $employees_timetables_header_ids; // 1 -- ok


//                     // getting vdate of this fingerprint
//                     $vdate_day = Carbon::parse($fingerprint->vdate)->format('Y-m-d');

//                     //return $vdate_day;
                        
//                     // based on TODAY and employee header ids, get timetable header ids
//                     $employees_timetables_header = 
//                     EmployeesTimetablesHeader::whereIn('id', $employees_timetables_header_ids)
//                     ->where('start_from', '<=', $vdate_day) 
//                     ->orderBy('start_from', 'desc')
//                     ->orderBy('id', 'desc')
//                     ->first();

//                     //return $employees_timetables_header;

//                     // No VALID day detail -- 1 (no corresponding timetable header id for that employee on that day)
//                     // skip to next row
//                     if(!$employees_timetables_header) 
//                         continue;
                    

//                     $timetables_header_id = $employees_timetables_header['timetables_header_id'];


//                     //return $timetables_header_id;
                   

//                     // getting shift type (1: fixed time, one shift, 2: fixed time, two shifts)
//                     $timetableHeader = TimeTablesHeader::where('id', $timetables_header_id)->first();

//                     //return $timetableHeader;

//                     // No VALID day detail -- 2 (no corresponding shift-type in TimeTableHeader)
//                     if(!$timetableHeader) 
//                         continue;

//                     $timetable_types_id = $timetableHeader['timetable_types_id'];

                
//                     //return $timetable_types_id;

//                     $shiftTypeId = TimeTableType::where('id', $timetable_types_id)->first();

//                     //return $shiftTypeId;


//                     // No VALID day detail -- 3 (No corresponding shift-type in TimeTableType)
//                     // skip to next row
//                     if(!$shiftTypeId) 
//                         continue;
                    
//                     $shiftTypeId = $shiftTypeId['id'];
//                     //return $shiftTypeId; // 2 (2 shifts)
                    
                    
//                     // day as number
//                     $currentDateDayNo = Carbon::parse($vdate_day)->dayOfWeekIso; // Monday: 1, Sunday: 7

//                     //return $currentDateDayNo;

//                     // get day detail where the shifts exact start and end times are set
//                     $dayDetail =
//                     TimeTablesDetail::where('timetables_header_id', $timetables_header_id)
//                     ->where('day_no', $currentDateDayNo)
//                     ->first(); // id: 26, day_no: 1

//                     //return $dayDetail;

//                     // case no day detail, skip to next row
//                     if(!$dayDetail) 
//                         continue;

//                     //return $dayDetail;

//                     // before getting all the details let us check first if the day in the day detail is OFF 
//                     $is_off = $dayDetail['is_off'];

//                     //return $is_off; // 0
                   
//                     // case day is OFF
//                     // skip to next row
//                     if($is_off == 1)
//                         continue; // go next day
                    
//                     $fingerprintTime = Carbon::parse($fingerprint->vdate)->format('H:i:s');

//                     // getting shift1 range
                        
//                     $shift1_from =  $dayDetail['shift1_from']; // 08:00:00
//                     $shift1_to =  $dayDetail['shift1_to']; // 11:00:00
                    
//                     $timePreIn_1 = Carbon::parse($shift1_from)->subMinutes($minsPreIn)->format('H:i:s'); // 07:30:00
//                     $timePostIn_1 = Carbon::parse($shift1_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
//                     $timeAbsentIn_1 = Carbon::parse($shift1_from)->addMinutes($minsAbsentIn)->format('H:i:s'); // 09:00:00
                
//                     $timePreOut_1 = Carbon::parse($shift1_to)->subMinutes($minsPreOut)->format('H:i:s'); // 17:00:00
//                     $timePostOut_1 = Carbon::parse($shift1_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00
//                     $timeMaxOut_1 = Carbon::parse($shift1_to)->addMinutes($minsMaxOut)->format('H:i:s'); // 19:00:00

//                     //return $timeMaxOut_1;

//                     //return [$shift1_from, $shift1_to, $timePreIn_1, $timeMaxOut_1];

//                     //return $shiftTypeId;

//                     // has two shifts ? => which shift this fingerprint belongs to?
//                     if($shiftTypeId == 2){
                                          
//                         // getting shift2 range
                            
//                         $shift2_from =  $dayDetail['shift2_from']; // 14:00:00
//                         $shift2_to =  $dayDetail['shift2_to']; // 18:00:00

//                         $timePreIn_2 = Carbon::parse($shift2_from)->subMinutes($minsPreIn)->format('H:i:s'); // 07:30:00
//                         $timePostIn_2 = Carbon::parse($shift2_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
//                         $timeAbsentIn_2 = Carbon::parse($shift2_from)->addMinutes($minsAbsentIn)->format('H:i:s'); // 09:00:00
                    
//                         $timePreOut_2 = Carbon::parse($shift2_to)->subMinutes($minsPreOut)->format('H:i:s'); // 17:00:00
//                         $timePostOut_2 = Carbon::parse($shift2_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00
//                         $timeMaxOut_2 = Carbon::parse($shift2_to)->addMinutes($minsMaxOut)->format('H:i:s'); // 19:00:00
    
//                         //return $timeMaxOut_2;

//                         //return [$fingerprintTime, $timePreIn_1, $timeMaxOut_1, $timePreIn_2, $timeMaxOut_2];
                        
//                         //return $fingerprintTime;

//                         if($fingerprintTime >= $timePreIn_1 && $fingerprintTime <= $timeMaxOut_1)
//                             $shiftType = "shift 1"; // 1st shift fingerprint

//                         else if($fingerprintTime >= $timePreIn_2 && $fingerprintTime <= $timeMaxOut_2)
//                             $shiftType = "shift 2"; // 2nd shift fingerprint
                        
//                         else // absence case ... (to be continued)
//                             continue;



//                         //return $shiftType;    

//                     }
//                     else if($shiftTypeId == 1) 
//                         $shiftType= "shift"; // NOT two shifts

//                     // other types of shifts (beside shift 1 & shift 2)
//                     /*
//                     else{

//                     }
//                     */
//                     // return $fingerprintMessages['shift_type'];

//                     // until here we know which shift this fingerprint belongs

//                     // case only one shift or 1st shift
//                     if($shiftType == "shift" || $shiftType == "shift 1"){
    
//                         $timePostIn_1 = Carbon::parse($shift1_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
//                         $timePostOut_1 = Carbon::parse($shift1_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00

//                         // initialize
//                         $requestMins = 0;
                        
//                         // attendance (late-time in mins)
                        
//                         if($fingerprint->att_type == 0 && $fingerprintTime > $timePostIn_1 && $fingerprintTime <= $timeAbsentIn_1)
//                         {
//                             $requestMins = Carbon::parse($fingerprintTime)->diffInMinutes(Carbon::parse($shift1_from)); 
//                             $messageTypeId = 1; // late
//                         }    

//                         // departure (over-time in mins)
//                         if($fingerprint->att_type == 1 && $fingerprintTime > $timePostOut_1 && $fingerprintTime <= $timeMaxOut_1)
//                         {
//                             $requestMins = Carbon::parse($fingerprintTime)->diffInMinutes(Carbon::parse($shift1_to)); 
//                             $messageTypeId = 2; // overtime
//                         }    

//                         //return $requestMins;
//                         // no message for employee who does NOT have delay time or over time
//                         if($requestMins == 0)
//                             continue;
                       
//                     } // case only one shift or 1st shift

                    
                    

//                     // case 2nd shift
//                     else if($shiftType == "shift 2"){
                        
//                         $timePostIn_2 = Carbon::parse($shift2_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
//                         $timePostOut_2 = Carbon::parse($shift2_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00

//                         // initialize
//                         $requestMins = 0;
                       
//                         // Attendance (late-time in mins)

//                         if($fingerprint->att_type == 0 && $fingerprintTime > $timePostIn_2 && $fingerprintTime <= $timeAbsentIn_2)
//                         {
//                             $requestMins = Carbon::parse($fingerprintTime)->diffInMinutes(Carbon::parse($shift2_from)); 
//                             $messageTypeId = 1; // late
//                         }    

//                         // Departure (over-time in mins)
//                         if($fingerprint->att_type == 1 && $fingerprintTime > $timePostOut_2 && $fingerprintTime <= $timeMaxOut_2)
//                         {
//                             $requestMins = Carbon::parse($fingerprintTime)->diffInMinutes(Carbon::parse($shift2_to)); 
//                             $messageTypeId = 2; // overtime
//                         }    

//                         // no message for employee who does NOT have delay time or over time
//                         if($requestMins == 0)
//                             continue;
                        
//                     } // case 2nd shift

//                     //return $fingerprintMessages;

//                     $fingerprintMessages[$row]['employee_id'] = $employee_id;
//                     $fingerprintMessages[$row]['message_date'] = $vdate_day;
//                     $fingerprintMessages[$row]['message_type_id'] = $messageTypeId;
//                     $fingerprintMessages[$row]['request_mins'] = $requestMins;
//                     $fingerprintMessages[$row]['shift_no'] = $shiftType;
//                     $fingerprintMessages[$row]['module'] = $request->module_type;
                    
//                     $row++;                    

//                     //return $employee_id;

      
//             } // looping over received fingerprints

//             // filling general_message_request table

//             //return $fingerprintMessages;

            
//             //return 100;

//             // inserting into the general_message_requests db
//             foreach(collect($fingerprintMessages)->chunk(50) as $chunk)
//             {
//                 //return $chunk->toArray();
//                 //return 100;
                       
//                 DB::transaction(function () use($chunk) {
//                     GeneralMessageRequest::insert($chunk->toArray());
//                 }, 5);
                
//             }
            
//             // return 100;

//             //$fingerprintMessages['id']

//         // collecting needed data for the general_message_receiver_contacts
            
//             $needProcessRequests = 
//             DB::table('general_message_requests')->where('status', "not processed")->get();
            
//             // case no new messages to process
//             if(sizeOf($needProcessRequests) == 0)
//                 return;

//             $messageReceiverContacts = [];

//             // need to fill (message_request_id, message_employee_id, client_type_id, client_id, sent(default:0))
//             // looping over each message request 

//             $i = 0;

//             //return $needProcessRequests;

//             foreach($needProcessRequests as $request){

//                 $clientId = $request->employee_id;

//                 // getting employee managers
//                 $managerIds = DB::table('general_employee_managers')->where('employee_id', $clientId)->pluck('manager_id');
                

//                 //return $managerIds;

//                 // case NO managers to send to 
//                 if(sizeOf($managerIds) == 0)
//                     continue;
                
//                 // getting client
//                 $clientTypeId = DB::table('general_client_types')->where('name_e', 'employee')->value('id');
                
//                 // case NO clientType exists for person's type: employee
//                 if(!$clientTypeId)
//                     continue;

//                 // message to the employee himself/herself beside managers?
//                 if($messageToEmployee == 1)
//                     $managerIds->push($clientId);
                
//                 //return $managerIds;


//                 foreach($managerIds as $managerId){

//                     $messageReceiverContacts[$i]['message_request_id'] = $request->id;
//                     $messageReceiverContacts[$i]['manager_employee_id'] = $managerId;
//                     $messageReceiverContacts[$i]['client_type_id'] = $clientTypeId;
//                     $messageReceiverContacts[$i]['client_id'] = $clientId;

//                     $i++;
//                 }


//             }

//             // return 100;

//             //return $messageReceiverContacts;

//             //return sizeOf($messageReceiverContacts);
           
            
//             // inserting into the general_message_receiver_contacts db
//             foreach(collect($messageReceiverContacts)->chunk(20) as $chunk)
//             {
//                 DB::transaction(function () use($chunk) {
//                     MessageReceiverContact::insert($chunk->toArray());          
//                 }, 5);
//             }
            

//             $model = MessageReceiverContact::latest()->first();


//             $model->processMessageRequest();

//             return 100;

//             /*
//             foreach($messageReceiverContacts as $record){
                
//                 //return $record;
//                 $p = new MessageReceiverContact;
//                 $p->message_request_id = $record['message_request_id'];
//                 $p->manager_employee_id = $record['manager_employee_id'];
//                 $p->client_id = $record['client_id'];
//                 $p->client_type_id = $record['client_type_id'];
//                 $p->save();
//                 unset($p);

//             }
//             */
            
//             //$messageReceiverContacts = GeneralMessageRequest::where('status', 'not processed')->first();

//             //return $messageReceiverContacts->pluck('id');//->toArray();

//             //return MessageReceiverContact::whereIn('message_request_id', $messageReceiverContacts->pluck('id'))->get();

//             //event('eloquent.created: ' . MessageReceiverContact::class, $messageReceiverContacts);
//             //$updatedRequests = GeneralMessageRequest::where('status', 'not processed');
//             //$updatedRequests->update(['status' => 'processed']);
//             //$updatedRequests->save();
            
// /*
//         // preparing the to-be-sent message body

//             // depends on employee favourite language (ar or en)
//             $messageLang = "en"; //$request->input('type'); 

//             $messageBodies = [];

//             $j = 0;
//             foreach($needProcessRequests as $request){
                
//                 //return $request->message_type_id;

//                 if($messageLang == "ar")
//                     $templateMessage = DB::table('general_messages')
//                     ->where('message_type_id', $request->message_type_id)
//                     ->value('content');

//                 else if($messageLang == "en")
//                     $templateMessage = DB::table('general_messages')
//                     ->where('message_type_id', $request->message_type_id)
//                     ->value('content_e');

//                 $employeeName = DB::table('general_employees')->where('id', $request->employee_id)->value('name');

//                     $messageBodies[$j]['message_body'] = 
//                     str_replace
//                     (
//                         ['{name}', '{date}', '{day}', '{mins}', "\n"], 
//                         [$employeeName, $request->message_date, Carbon::parse($request->message_date)->format('l'), $request->request_mins, ' '], $templateMessage
//                     );
                    
//                     //return $message;

//                     $receiversIds = DB::table('general_message_receiver_contacts')
//                     ->where('message_request_id', $request->id)->pluck('manager_employee_id');

//                     $phones = [];
//                     foreach ($receiversIds as $receiverId){
                        
//                         $phones[] = DB::table('general_employees')->where('id', $receiverId)->value('whatsapp');

//                     }


//                     $messageBodies[$j]['whatsapps'] = $phones;

//                     $j++;
                
//             }

//             return $messageBodies;
// */                    

//             //return response()->json(['updated_messages' => $updatedMessages]);
//         //} // try
//         //catch (\Exception $e) {
//         //    return response()->json(['error' => $e->getMessage()]);
//         //}


//     } // sendMessage(Request $request)
    

    public function sendHRMessage(Request $request){
        
/*
        $fingerprint = Fingerprint::create(
            [
                'att_code' => 18, 
                'vdate' => '2023-06-10 08:45:01',
                'att_type' => 0,
                'machine_id' => 1,
            ]
        );
*/
        
        //$this->sendMessage($request);

    }

    


} // class MessageController


