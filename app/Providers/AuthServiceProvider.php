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

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,

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
