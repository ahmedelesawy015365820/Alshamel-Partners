<?php

namespace App\Observers;

use App\Models\MessageReceiverContact;
use App\Models\GeneralMessageRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Employee;

class MessageReceiverContactObserver
{
    /**
     * Handle the MessageReceiverContact "created" event.
     *
     * @param  \App\Models\MessageReceiverContact  $messageReceiverContact
     * @return void
     */
    public function processedMessageRequests(MessageReceiverContact $messageReceiverContact)
    {
        
        //Log::info('Creating event call: '.$messageReceiverContacts->get());
        
        // preparing the to-be-sent message body

            // depends on employee favourite language (ar or en)
            //$messageLang = "en"; //$request->input('type'); 

            $messageBodies = [];

            $needProcessRequests = DB::table('general_message_requests')->where('status', 'not processed')->get();

            //return $needProcessRequests;

            $j = 0;
            foreach($needProcessRequests as $request){
                
                //return $request->message_type_id;

                $messageLang = Employee::find($request->employee_id)->preferred_lang;

                if($messageLang == "ar")
                    $templateMessage = DB::table('general_messages')
                    ->where('message_type_id', $request->message_type_id)
                    ->value('content');

                else if($messageLang == "en")
                    $templateMessage = DB::table('general_messages')
                    ->where('message_type_id', $request->message_type_id)
                    ->value('content_e');

                $employeeName = DB::table('general_employees')->where('id', $request->employee_id)->value('name');

                $messageBodies[$j]['message_body'] = 
                    str_replace
                    (
                        ['{name}', '{date}', '{day}', '{mins}', "\n"], 
                        [$employeeName, $request->message_date, Carbon::parse($request->message_date)->format('l'), $request->request_mins, ' '], $templateMessage
                    );
                    
                //return $message;

                $receiversIds = DB::table('general_message_receiver_contacts')
                ->where('message_request_id', $request->id)->pluck('manager_employee_id');

                $phones = [];
                foreach ($receiversIds as $receiverId){
                    
                    $phones[] = DB::table('general_employees')->where('id', $receiverId)->value('whatsapp');

                }

                $messageBodies[$j]['whatsapps'] = $phones;

                $j++;
                
            }
            
            DB::table('general_message_receiver_contacts')->where('sent', 0)->update(['sent' => 1]);

            return $messageBodies;  
        
   
    } // public function created

    /**
     * Handle the MessageReceiverContact "updated" event.
     *
     * @param  \App\Models\MessageReceiverContact  $messageReceiverContact
     * @return void
     */
    public function updated(MessageReceiverContact $messageReceiverContact)
    {
        //
    }

    /**
     * Handle the MessageReceiverContact "deleted" event.
     *
     * @param  \App\Models\MessageReceiverContact  $messageReceiverContact
     * @return void
     */
    public function deleted(MessageReceiverContact $messageReceiverContact)
    {
        //
    }

    /**
     * Handle the MessageReceiverContact "restored" event.
     *
     * @param  \App\Models\MessageReceiverContact  $messageReceiverContact
     * @return void
     */
    public function restored(MessageReceiverContact $messageReceiverContact)
    {
        //
    }

    /**
     * Handle the MessageReceiverContact "force deleted" event.
     *
     * @param  \App\Models\MessageReceiverContact  $messageReceiverContact
     * @return void
     */
    public function forceDeleted(MessageReceiverContact $messageReceiverContact)
    {
        //
    }
}
