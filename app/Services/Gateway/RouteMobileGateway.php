<?php
namespace App\Services\Gateway;

use App\Contracts\RouteMobileContract;
use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;
use Illuminate\Support\Facades\Http;
use Exception;

class RouteMobileGateway implements RouteMobileContract
{
    public function sendBulkSmsBd(RouteMobileBulkSmsDTO $dto): array
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
                'raw'     => $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'raw'     => $e->getMessage(),
            ];
        }
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
}
