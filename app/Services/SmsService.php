<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    /**
     * Send SMS using InfoText API.
     *
     * @param  string $mobile
     * @param  string $sms
     * @return mixed
     */
    public function infoTextSend($mobile, $sms)
    {
        $sms_data = [
            'UserID' => '669',
            'ApiKey' => '207bb08817f8ab47ac813b6b8917de0d',
            'Mobile' => $mobile,
            'SMS' => $sms,
        ];

        // Send the request using Laravel's HTTP client
        $response = Http::post('https://api.myinfotxt.com/v2/send.php', $sms_data);

        // Return the response for debugging or further processing if needed
        return $response->json();
    }
}
