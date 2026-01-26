<?php
namespace App\Services\Gateway;

use App\Contracts\RouteMobileContract;
use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;
use App\Models\RmBulkSms;
use Illuminate\Support\Facades\Http;
use Exception;

use function Symfony\Component\Clock\now;

class RouteMobileGateway implements RouteMobileContract
{
    public function sendBulkSmsBd(RouteMobileBulkSmsDTO $dto): array
    {
        $response = $this->curlForSendBulkSmsBD($dto);

        $resBody = $response['res_body'];
        [$code, $mobile, $messageId] = explode('|', $resBody);

        RmBulkSms::create([
            'to' => $mobile,
            'message' => $dto->message,
            'message_id' => $messageId,
            'status' => $response['success'],
            'status_code' => $code,
            'response' => $response['res_body'],
            'sent_at' => now(),
            'delivered_at' => $response['success'] ? now() : null,
        ]);

        return $response;

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
            'message_id' => $payload['messageId'] ?? null,
            'status'     => $payload['status'] ?? null,
            'delivered_at' => $payload['deliveredAt'] ?? null,
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
}
