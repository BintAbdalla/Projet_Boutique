<?php

namespace App\Listeners;

use App\Events\PhotoUploadedEvent;
use App\Jobs\PhotoJob;

class ProcessPhotoUploadListener
{
    /**
     * Handle the event.
     *
     * @param  PhotoUploadedEvent  $event
     * @return void
     */
    public function handle(PhotoUploadedEvent $event)
    {
        // Dispatch du Job pour l'upload de la photo
        PhotoJob::dispatch($event->file);
    }
}
