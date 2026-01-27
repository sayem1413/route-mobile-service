<?php
namespace App\Contracts;

use App\DTOs\SmsMessageDTO;
use App\DTOs\RouteMobileBulkSmsDTO;

interface RouteMobileContract
{
    public function sendSmsBd(RouteMobileBulkSmsDTO $dto): array;
}
