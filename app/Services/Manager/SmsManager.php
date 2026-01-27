<?php
namespace App\Services\Manager;

use App\Contracts\SMSDriverContract;
use App\Services\SMSDriverService;

class SmsManager
{
    public function driver($driver): SMSDriverContract
    {
        return match ($driver) {
            'route_mobile' => app(SMSDriverService::class),
            default => throw new \Exception('SMS driver not supported'),
        };
    }
}
