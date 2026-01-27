<?php

namespace App\Http\Controllers;

use App\Services\Manager\SmsManager;
use App\Models\RmBulkSms;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SmsCallbackController extends Controller
{
    protected SmsManager $smsManager;

    public function __construct(SmsManager $smsManager)
    {
        $this->smsManager = $smsManager;
    }

    public function routeMobile(Request $request)
    {
        $data = $this->smsManager->driver('route_mobile')->parseRmDlrCallbackData($request->all());

        RmBulkSms::where('message_id', $data['sMessageId'])
            ->update([
                'status' => $data['status'],
                'delivered_at' => Carbon::now(),
            ]);

        return response()->json(['ok' => true]);
    }
}
