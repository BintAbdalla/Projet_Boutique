<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\SmsServiceInterface;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|phone_number',
            'message' => 'required|string',
        ]);

        $response = $this->smsService->send($validated['to'], $validated['message']);

        return response()->json($response);
    }
}