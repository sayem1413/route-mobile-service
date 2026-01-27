<?php

namespace App\Services\Gateway;

use App\Contracts\RouteMobileContract;
use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;
use App\Models\RmBulkSms;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Exception;

use function Symfony\Component\Clock\now;

class RouteMobileGateway implements RouteMobileContract
{
    public function sendBulkSmsBd(RouteMobileBulkSmsDTO $dto): array
    {
        $response = $this->curlForSendBulkSmsBD($dto);

        $resStatus = $response['success'];
        $resBody = $response['res_body'];
        $statusCode = null;
        $mobile     = $dto->destination;
        $messageId  = null;
        $gatewayError = null;

        if ($resStatus === true && str_contains($resBody, '|')) {
            $parsed = $this->parseRouteMobileResponse($resBody);

            $statusCode = $parsed['status_code'];
            $mobile     = $parsed['mobile'] ?? $dto->destination;
            $messageId  = $parsed['message_id'];
        } elseif (!$resStatus) {
            $gatewayError = $resBody;
        }

        $remBulkSms = RmBulkSms::create([
            'to' => $dto->destination ?: $mobile,
            'message' => $dto->message,
            'message_id' => $messageId,
            'status' => ($resStatus && $messageId) ? RmBulkSms::STATUS_SENT : RmBulkSms::STATUS_FAILED,
            'status_code' => $statusCode,
            'response' => $resBody,
            'gateway_error' => $gatewayError,
            'sent_at' => Carbon::now(),
        ]);

        return $remBulkSms->toArray();
    }

    public function sendBulk(SmsMessageDTO $message): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('sms.route_mobile.token'),
        ])->post(config('sms.route_mobile.endpoint'), [
            'to'      => $message->recipients,
            'message' => $message->message,
            'sender'  => $message->senderId,
        ]);

        return $response->json();
    }

    public function parseDeliveryReport(array $payload): array
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

    private function curlForSendBulkSmsBD(RouteMobileBulkSmsDTO $dto)
    {
        $data = [
            'username'    => config('services.rml_bd.username'),
            'password'    => config('services.rml_bd.password'),
            'dlr'         => 1,
            'type'        => 0,
            'source'      => config('services.rml_bd.source'),
            'destination' => '88' . $dto->destination,
            'message'     => $dto->message,
        ];

        try {
            $response = Http::withQueryParameters($data)
                ->acceptJson()
                ->get(config('services.rml_bd.url'));

            return [
                'success' => $response->ok(),
                'res_body'     => $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'res_body'     => $e->getMessage(),
            ];
        }
    }

    private function parseRouteMobileResponse(string $resBody): array
    {
        [$status, $mobile, $messageId] = array_pad(
            explode('|', trim($resBody)),
            3,
            null
        );

        return [
            'status_code' => $status,
            'mobile'      => $mobile,
            'message_id'  => $messageId,
        ];
    }
}
