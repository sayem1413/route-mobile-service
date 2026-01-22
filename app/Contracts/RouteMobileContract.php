<?php
namespace App\Contracts;

use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;

interface RouteMobileContract
{
    public function sendBulkSmsBd(RouteMobileBulkSmsDTO $dto): array;
    
    public function sendBulk(SmsMessageDTO $message): array;

    public function parseDeliveryReport(array $payload): array;
}
