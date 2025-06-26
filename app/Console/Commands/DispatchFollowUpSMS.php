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
            // Ensure SMS message exists to avoid null errors
            if (!$followUp->smsMessage) {
                Log::warning("⚠️ No smsMessage for FollowUp ID: {$followUp->id}");
                continue;
            }

            $createdAt = Carbon::parse($followUp->created_at);
            $intervalDays = $followUp->smsMessage->interval;

            // Remove time from both dates by using toDateString()
            $scheduledDate = $createdAt->addDays($intervalDays)->toDateString();
            $currentDate = now()->toDateString();

            if ($currentDate >= $scheduledDate) {
                dispatch(new SendFollowUpSMS($followUp->id));

                $followUp->status = 'sent';
                $followUp->save();

                Log::info("📤 Dispatched follow-up ID {$followUp->id} and marked as sent.");
            }
        }

        $this->info('✅ Follow-up SMS jobs dispatched and statuses updated.');
    }
}
