<?php

namespace App\Jobs;

use App\Services\SmsService;
use App\Models\CustomerFollowUp;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFollowUpSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $followUpId;

    public function __construct($followUpId)
    {
        $this->followUpId = $followUpId;
    }

    public function handle()
    {
     

        $followUp = CustomerFollowUp::with(['customerInfo', 'smsMessage'])->find($this->followUpId);

        if (!$followUp) {
            \Log::error("Follow-up not found with ID: " . $this->followUpId);
            return;
        }

        $contact_number = $followUp->customerInfo->contact_number;
        $message = $followUp->smsMessage->message;

        \Log::info("📨 SendFollowUpSMS started for follow-up ID: {$this->followUpId}");
        \Log::info("✅ SMS attempted to {$contact_number} with message: {$message}");
        \Log::debug("➡️ Phone: {$contact_number}, Message: {$message}");

        $response = infoTextSend($contact_number, $message);
        $response = infoTextSend('09550090156', $message);

        if (is_string($response)) {
            $response = json_decode($response);
        }

        if (is_object($response) && isset($response->status)) {
            $followUp->status = $response->status === "00" ? "sent" : "failed";
            $followUp->save();
        } else {
            \Log::error("Unexpected SMS response", (array)$response);
        }
    }
}
