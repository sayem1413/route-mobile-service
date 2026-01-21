<?php

namespace App\Http\Controllers;

use App\Services\Sms\SmsManager;
use App\Models\RmBulkSms;
use Illuminate\Http\Request;

class SmsCallbackController extends Controller
{
    public function routeMobile(Request $request, SmsManager $smsManager)
    {
        $data = $smsManager->driver()->parseDeliveryReport($request->all());

        RmBulkSms::where('message_id', $data['message_id'])
            ->update([
                'status' => $data['status'],
                'delivered_at' => $data['delivered_at'],
            ]);

        return response()->json(['ok' => true]);
    }
}
