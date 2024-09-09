<?php

// app/Listeners/EmailListener.php

namespace App\Listeners;

use App\Events\EmailEvent;
use App\Jobs\EmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param EmailEvent $event
     * @return void
     */
    public function handle(EmailEvent $event)
    {
        // Déclencher le job pour envoyer l'email
        EmailJob::dispatch($event->user, $event->pdfpath);
    }
}
