<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerFollowUp;
use App\Models\SmsMessage;
use App\Models\CustomerInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmsController extends Controller
{
    public function create_customer_info(Request $request){
        $request->validate([
            'name' => 'required|string',
            'contact_number' => 'required|string',
        ]);
    
        $customer = CustomerInfo::create($request->only('name', 'contact_number'));
        $messages = SmsMessage::all();

        foreach ($messages as $follow_up_message) {
            CustomerFollowup::create([
                'customer_info_id' => $customer->id,
                'sms_message_id' => $follow_up_message->id,
            ]);
        }

        return response()->json([
            'message' => 'customer info created successfully!',
            'sms_message' => $customer
        ], 201);
    }
    
    public function create_sms_message(Request $request){
        
        $validatedData = $request->validate([
            'message_name' => 'required|string|max:255',
            'message' => 'required|string',
            'interval' => 'required|integer',
        ]);

        $smsMessage = SmsMessage::create([
            'message_name' => $validatedData['message_name'],
            'message' => $validatedData['message'],
            'interval' => $validatedData['interval'],
        ]);

        return response()->json([
            'message' => 'SMS message created successfully!',
            'sms_message' => $smsMessage
        ], 201);

        
    }

    public function get_sms_message(){
        $messages = SmsMessage::get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    public function get_single_sms_message($id){
        $message = SmsMessage::find($id);

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'SMS Message not found',
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $message,
        ]);
    }

    public function update_sms_message(Request $request, $id){

        // Find the SmsMessage by ID
        $smsMessage = SmsMessage::find($id);

        // Check if the SmsMessage exists
        if (!$smsMessage) {
            return response()->json(['error' => 'SMS Message not found'], 404);
        }

        // Update the SMS message details
        $smsMessage->message_name = $request->input('message_name');
        $smsMessage->message = $request->input('message');
        $smsMessage->interval = $request->input('interval');
        $smsMessage->updated_at = now(); // Update the timestamp for the update

        // Save the updated SMS message
        $smsMessage->save();

        return response()->json([
            'status' => 'success',
            'message' => 'SMS message updated successfully',
            'data' => $smsMessage
        ]);
        
    }

    public function delete_sms_message($id){
        // Find the SMS message by ID
        $smsMessage = SmsMessage::find($id);

        // Check if the SMS message exists
        if (!$smsMessage) {
            return response()->json([
                'status' => 'error',
                'message' => 'SMS Message not found'
            ], 404);
        }

        // Delete the SMS message
        $smsMessage->delete();

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'SMS Message deleted successfully'
        ], 200);
    }

    public function get_customer_follow_up(){
        $followUps = CustomerFollowUp::with(['customerInfo', 'smsMessage'])->where('status', 'pending')->get();

        $data = $followUps->map(function ($followUp) {
            $scheduledDate = $followUp->created_at->copy()->addDays($followUp->smsMessage->interval);
            $daysRemaining = now()->diffInDays($scheduledDate, false); // false keeps negative value

            return [
                'name' => $followUp->customerInfo->name,
                'contact_number' => $followUp->customerInfo->contact_number,
                'message_name' => $followUp->smsMessage->message_name,
                'interval' => $daysRemaining,
                'status' => $followUp->status,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }   

}


