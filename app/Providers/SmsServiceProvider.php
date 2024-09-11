<?php

// app/Providers/SmsServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\SmsServiceInterface;
use App\Services\TwilioService;
use App\Services\InfobipService;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //     $this->app->bind(SmsServiceInterface::class, function ($app) {
        //         $service = config('services.sms_service');

        //         if ($service === 'twilio') {
        //             return new twilioService();
        //         }

        //         if ($service === 'infobip') {
        //             return new InfobipService();
        //         }

        //         throw new \Exception("No SMS service defined");
        //     });


        $this->app->bind(SmsServiceInterface::class, TwilioService::class);
        // $this->app->bind(SmsServiceInterface::class,InfobipService::class);
    }
}
