<?php

namespace App\Http\Controllers;

use App\Services\Manager\SmsManager;
use App\Models\RmBulkSms;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SmsCallbackController extends Controller
{
    public function routeMobile(Request $request, SmsManager $smsManager)
    {
        $data = $smsManager->driver()->parseDeliveryReport($request->all());

        /* RmBulkSms::where('message_id', $data['sMessageId'])
            ->update([
                'status' => $data['status'],
                'delivered_at' => Carbon::now(),
            ]); */

        return response()->json(['ok' => true]);
    }
}
