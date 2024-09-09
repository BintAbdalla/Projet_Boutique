<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PhotoUploadedEvent;
use App\Listeners\ProcessPhotoUploadListener; // Assuming ProcessPhotoUploadListener is defined in your app's Listeners folder
use App\Events\EmailEvent;
use App\Listeners\EmailListener; // Assuming EmailListener is defined in your app's Listeners folder

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            PhotoUploadedEvent::class => [
                ProcessPhotoUploadListener::class,
                EmailEvent::class => [
                    EmailListener::class,
                ],
            ],
        ],
    ];


    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
