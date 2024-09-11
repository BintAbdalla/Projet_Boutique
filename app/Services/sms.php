<?php

namespace App\Services;

// use App\Services\Contracts\SmsServiceInterface;
use App\Models\Client;
use App\Models\Dettes;
use App\Notifications\SmsNotification;
use App\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Notification;

class sms
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSMS()
    {




$this->smsService->send('++221775752135','bonjour');








        // $dettes = Dettes::with('client')->get();
        // $clients = $dettes->map(function ($dette) {
        //     return $dette->client_id;
        // })->unique();  // Utilisation de unique() pour ne garder que des ids uniques

        // foreach ($clients as $client_id) {
        //     $client = Client::find($client_id);
        //     $this->smsService->sendSms($client->telephone, 'Bonjour '. $client->surnom . ' le montant de vos dettes est de : ' . $this->calculateAmountDue($client).' € .Merci de ');
        // }


        // foreach ($clients as $client_id) {
        //     $client = Client::find($client_id);

        //     $amountDue = $this->calculateAmountDue($client);
        //     $message = 'Bonjour, le montant de vos dettes est de : ' . $amountDue;

        //     // Envoi de la notification
        //     Notification::route('infobip', $client->telephone)
        //         ->notify(new SmsNotification($message));
        // }
    }




    // protected function calculateAmountDue(Client $client): float
    // {
    //     //recuperation des dettes
    //     $dettes = Dettes::where('client_id', $client->id)->get();
    //     //verifier si c'est payé ou non
    //     $amountDue = $dettes->sum(function ($dette) {
    //         return $dette->montant - $dette->paiements->sum('montant');

    //     });

    //     return $amountDue;
    // }
}