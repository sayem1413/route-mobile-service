<?php

namespace App\Services;

use App\Contracts\SMSDriverContract;
use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;
use App\Models\RmBulkSms;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Exception;

class SMSDriverService implements SMSDriverContract
{
    public function parseRmDlrCallbackData(array $payload): array
    {
        return [
            'sSender' => $payload['sSender'] ?? null,
            'sMobileNo' => $payload['sMobileNo'] ?? null,
            'dtSubmit' => $payload['dtSubmit'] ?? null,
            'dtDone' => $payload['dtDone'] ?? null,
            'sMessageId' => $payload['sMessageId'] ?? null,
            'iCostPerSms' => $payload['iCostPerSms'] ?? null,
            'iCharge' => $payload['iCharge'] ?? null,
            'iMCCMNC' => $payload['iMCCMNC'] ?? null,
            'iErrCode' => $payload['iErrCode'] ?? null,
            'sTagName' => $payload['sTagName'] ?? null,
            'sUdf1' => $payload['sUdf1'] ?? null,
            'sUdf2' => $payload['sUdf2'] ?? null,
        ];
    }
}
