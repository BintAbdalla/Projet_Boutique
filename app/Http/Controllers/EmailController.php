<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\MailFacade;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
            'name' => 'John Doe',
            'message' => 'This is a test email.'
        ];

        MailFacade::sendEmail($data, 'recipient@example.com');

        return response()->json(['message' => 'Email sent successfully']);
    }
}
