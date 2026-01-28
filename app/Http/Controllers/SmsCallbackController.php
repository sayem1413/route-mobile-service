<?php

namespace App\Http\Controllers;

use App\Services\Manager\SmsManager;
use App\Models\RmBulkSms;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SmsCallbackController extends Controller
{
    protected SmsManager $smsManager;

    public function __construct(SmsManager $smsManager)
    {
        $this->smsManager = $smsManager;
    }

    public function routeMobile(Request $request)
    {
        $data = $this->smsManager
            ->driver('route_mobile')
            ->parseRmDlrCallbackData($request->all());

        Log::info('Route Mobile DLR Callback', $data);

        if (empty($data['sMessageId'])) {
            return response()->json(['error' => 'Message ID missing'], 400);
        }

        $sms = RmBulkSms::where('message_id', $data['sMessageId'])->first();

        if (!$sms) {
            return response()->json(['error' => 'RMSMS Data not found'], 404);
        }
        $rmStatus = $data['sStatus'] ?? null;
        $finalStatus = RmBulkSms::mapRouteMobileStatus($rmStatus);

        if ($finalStatus === RmBulkSms::STATUS_DELIVERED) {
            $sms->markAsDelivered(
                $rmStatus,
                $data
            );
        } elseif ($finalStatus === RmBulkSms::STATUS_FAILED) {
            $sms->markAsFailed(
                $rmStatus,
                $data,
                "Delivery report (failed)"
            );
        } else {
            $sms->update([
                'status_code' => $rmStatus,
                'response'    => $data,
            ]);
        }

        return response()->json(['success' => true], 200);
    }
}
