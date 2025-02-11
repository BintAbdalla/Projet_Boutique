<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Repository\ArticleRepository;
use App\Repository\EloquentArticleRepository;
use App\Services\ClientService;
use App\Repository\ClientRepository;
use App\Repository\EloquentClientRepository;
use App\Services\ClientServiceImpl;
use App\Services\UploadService;
use App\Services\CloudUploadService;
use App\Services\MailService;
use App\Services\QrCodeService;
use App\Services\DetteService;
use App\Services\DetteServiceImpl;
use App\Repository\DetteRepository;
use App\Repository\EloquentDetteRepository;





class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
        $this->app->bind(ArticleRepository::class, EloquentArticleRepository::class);
        $this->app->bind(ClientService::class, ClientServiceImpl::class);
        $this->app->bind(ClientRepository::class, EloquentClientRepository::class);
        $this->app->bind(DetteRepository::class, EloquentDetteRepository::class);
        $this->app->bind(DetteService::class, DetteServiceImpl::class);
        // $this->app->bind(QrCodeService::class, EloquentClientRepository::class);



        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });
        $this->app->singleton(CloudUploadService::class, function ($app) {
            return new CloudUploadService();
        });


        // Enregistrer le service
        $this->app->singleton('mailservice', function ($app) {
            return new MailService();
        });

           // Enregistrer le service
           $this->app->singleton('qrCodeService', function ($app) {
            return new QrCodeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // passport::route();
    }
}
