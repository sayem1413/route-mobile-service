<?php
namespace App\Contracts;

use App\DTOs\RouteMobileSingleSmsDTO;

interface RouteMobileContract
{
    public function sendSmsBd(RouteMobileSingleSmsDTO $dto): array;
}
