<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\ExampleMail;

class MailService
{
    public function sendEmail(array $data, $recipient)
    {
        Mail::to($recipient)->send(new ExampleMail($data));
    }
}
