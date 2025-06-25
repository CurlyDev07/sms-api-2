<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmsService;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        /// Directly access request data
        $contactNumber = $request->input('contact_number');
        $message = $request->input('message');

        // Call the SmsService to send the message
        $smsService = new SmsService();
        $smsService->infoTextSend($contactNumber, $message);

        return response()->json([
            'message' => 'SMS sent successfully!'
        ]);
    }
}
