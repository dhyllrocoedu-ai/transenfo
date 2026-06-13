<?php

namespace App\Providers;

use App\Models\Appeal;
use App\Policies\AppealPolicy;
use App\View\Composers\NavigationComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Gate::policy(Appeal::class, AppealPolicy::class);
        View::composer('layouts.app', NavigationComposer::class);
    }
}
