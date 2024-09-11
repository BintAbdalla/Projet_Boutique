<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\sms;

class EssaieCommand extends Command
{
    // Nom et description de la commande
    protected $signature = 'photo:upload';

    protected $description = 'Description de ma commande personnalisée';
 protected $sms;
    // Création de la commande
    public function __construct(sms $sms )
    {
        parent::__construct();
        $this->sms = $sms;
    }

    public function handle()
    {
        // $user = $this->argument('user');
        // $message = $this->option('message') ?: 'Message par défaut';
    
        $this->info("Envoi du message");
        $this->sms->sendSMS();  // Appel de la fonction de service de sms
    
        // Ajoutez ici la logique pour envoyer le message
    }
    
}

