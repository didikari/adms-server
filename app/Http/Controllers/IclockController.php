<?php

namespace App\Http\Controllers;

use App\Services\DeviceService;
use Illuminate\Http\Request;

class IclockController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function handshake(Request $request)
    {
        return $this->deviceService->handshake($request);
    }

    public function receiveRecords(Request $request)
    {
        return $this->deviceService->receiveRecords($request);
    }
}
