<?php
namespace App\Services\Manager;

use App\Contracts\SmsGatewayInterface;
use App\Services\Gateways\RouteMobileGateway;

class SmsManager
{
    public function driver(): SmsGatewayInterface
    {
        return match (config('sms.default')) {
            'route_mobile' => app(RouteMobileGateway::class),
            default => throw new \Exception('SMS driver not supported'),
        };
    }
}
