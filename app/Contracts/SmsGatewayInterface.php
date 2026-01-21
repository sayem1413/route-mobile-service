<?php
namespace App\Contracts;

use App\DTOs\SmsMessageDTO;
use App\DTOs\SmsDTO;

interface SmsGatewayInterface
{
    public function send(SmsDTO $dto): array;
    
    public function sendBulk(SmsMessageDTO $message): array;

    public function parseDeliveryReport(array $payload): array;
}
