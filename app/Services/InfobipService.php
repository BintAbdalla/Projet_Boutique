<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Log;

class InfobipService implements SmsServiceInterface
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.infobip.api_url'),
            'headers' => [
                'Authorization' => 'App ' . config('services.infobip.api_key'),
                'Content-Type' => 'application/json',
            ],
            'debug' => true, // Ajoutez cette ligne pour activer le débogage
        ]);
        ;
    }

    public function send(string $to, string $message): array
    {
        try {
            $response = $this->client->post('/sms/2/text/advanced', [
                'json' => [
                    'messages' => [
                        [
                            'destinations' => [['to' => $to]],
                            'from' => $this->from,
                            'text' => $message,
                        ],
                    ],
                ],
            ]);
    
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log or handle the exception
            // Example: Log the error message
            Log::error('InfobipService error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    
}
