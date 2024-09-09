<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;
use App\Policies\ClientPolicy;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use App\Services\AuthServiceInterface;
use App\Services\SanctumAuthService;
use App\Services\PassportAuthService;

class AuthServiceProvider extends ServiceProvider
{



    public function register()
    {
        $this->app->singleton(AuthServiceInterface::class, function ($app) {
            // Switch between Sanctum and Passport based on configuration
            // dd(config('auth.defaults.auth'));
            return config('auth.defaults.auth') === 'sanctum'
                ? new SanctumAuthService()
                : new PassportAuthService();
        });
    }
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,
        'App\Models\Article' => 'App\Policies\ArticlePolicy',
        'App\Models\Client' => 'App\Policies\ClientPolicy',


    ];

    // protected $policies = [
    //     Client::class => ClientPolicy::class,
    // ];
    public function boot()
    {
        $this->registerPolicies();

        // Gate::define('admin',[UserPolicy::class]);

    }
}
