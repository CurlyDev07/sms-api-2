<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerFollowUp;
use App\Jobs\SendFollowUpSMS;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DispatchFollowUpSMS extends Command
{
    protected $signature = 'sms:dispatch';
    protected $description = 'Dispatch follow-up SMS based on sms message interval';

    public function handle()
    {
        Log::info('🕐 Running sms:dispatch command at ' . now());

        $followUps = CustomerFollowUp::with(['customerInfo', 'smsMessage'])
            ->where('status', 'pending')
            ->get();

        foreach ($followUps as $followUp) {
            $createdAt = Carbon::parse($followUp->created_at);
            $intervalDays = $followUp->smsMessage->interval;

            $scheduledTime = $createdAt->copy()->addDays($intervalDays); // Carbon instance
            $now = Carbon::now(); // Carbon instance

            $diffInDays = $now->diffInDays($scheduledTime, false); // Optional: can log this

            if ($now->greaterThanOrEqualTo($scheduledTime)) {
                dispatch(new SendFollowUpSMS($followUp->id));
                Log::info("📤 Dispatched follow-up ID {$followUp->id}");
            }
        }

        $this->info('Follow-up SMS jobs dispatched!');
    }
}
