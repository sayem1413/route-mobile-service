<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendBulkSmsBdRequest;
use App\Contracts\RouteMobileContract;
use App\DTOs\RouteMobileBulkSmsDTO;

class RouteMobileSMSController extends Controller
{
    protected RouteMobileContract $routeMobileService;

    public function __construct(RouteMobileContract $routeMobileService)
    {
        $this->routeMobileService = $routeMobileService;
    }

    public function sendBulkSmsBd(SendBulkSmsBdRequest $request)
    {
        $dto = new RouteMobileBulkSmsDTO(
            destination: $request->validated('destination'),
            message: $request->validated('message'),
        );
        $bulkSmsBdResponse = $this->routeMobileService->sendBulkSmsBd($dto);

        return response()->json([
            'data' => $bulkSmsBdResponse
        ], 200);
    }
}
