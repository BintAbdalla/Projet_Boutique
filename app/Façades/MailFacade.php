<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MailFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailservice'; // Le nom du service dans le conteneur de services
    }
}
