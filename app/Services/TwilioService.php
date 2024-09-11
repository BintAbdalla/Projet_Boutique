<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Contracts\SmsServiceInterface;

class TwilioService implements SmsServiceInterface
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(config('services.twilio.sid'), config('services.twilio.auth_token'));
        $this->from = config('services.twilio.phone_number');
    
        // Debugging
        // var_dump(config('services.twilio.sid'));  // Doit afficher le SID Twilio
        // var_dump(config('services.twilio.auth_token'));  // Doit afficher le Token Twilio
        // var_dump(config('services.twilio.phone_number'));  // Doit afficher le numéro Twilio
    }
    
    

    public function send(string $to, string $message): array
    {
        $message = $this->client->messages->create($to, [
            'from' => $this->from,
            'body' => $message,
        ]);

        return [
            'status' => $message->status,
            'sid' => $message->sid,
        ];
    }
}
