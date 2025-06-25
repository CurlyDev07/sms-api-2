<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFollowUp extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customerInfo()
    {
        return $this->belongsTo(CustomerInfo::class);
    }

    // Relationship to SmsMessage
    public function smsMessage()
    {
        return $this->belongsTo(SmsMessage::class);
    }

}
