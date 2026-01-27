<?php

namespace App\Services\Gateway;

use App\Contracts\RouteMobileContract;
use App\DTOs\RouteMobileBulkSmsDTO;
use App\Models\RmBulkSms;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Exception;

class RouteMobileGateway implements RouteMobileContract
{
    public function sendSmsBd(RouteMobileBulkSmsDTO $dto): array
    {
        $response = $this->curlForRMSendSmsBD($dto);

        $resStatus = $response['success'];
        $resBody = $response['res_body'];
        $statusCode = null;
        $mobile     = $dto->destination;
        $messageId  = null;
        $gatewayError = null;

        if ($resStatus === true && str_contains($resBody, '|')) {
            $parsed = $this->parseRMSmsResponse($resBody);

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

    private function curlForRMSendSmsBD(RouteMobileBulkSmsDTO $dto)
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

    private function parseRMSmsResponse(string $resBody): array
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
