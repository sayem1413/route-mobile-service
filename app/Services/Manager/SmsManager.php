<?php
namespace App\Services\Manager;

use App\Contracts\RouteMobileContract;
use App\Services\Gateway\RouteMobileGateway;

class SmsManager
{
    public function driver(): RouteMobileContract
    {
        return match ('route_mobile') {
            'route_mobile' => app(RouteMobileGateway::class),
            default => throw new \Exception('SMS driver not supported'),
        };
    }
}
