<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendSingleRMSmsBdRequest;
use App\Contracts\RouteMobileContract;
use App\DTOs\RouteMobileSingleSmsDTO;

class RouteMobileSMSController extends Controller
{
    protected RouteMobileContract $routeMobileService;

    public function __construct(RouteMobileContract $routeMobileService)
    {
        $this->routeMobileService = $routeMobileService;
    }

    public function sendBulkSmsBd(SendSingleRMSmsBdRequest $request)
    {
        try {
            $dto = RouteMobileSingleSmsDTO::fromArray(
                $request->validated()
                // []
            );
        
            $bulkSmsBdResponse = $this->routeMobileService->sendSmsBd($dto);

            return response()->json([
                'data' => $bulkSmsBdResponse
            ], 200);
        } catch (\Exception $e) {
            dd('Ok');
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
