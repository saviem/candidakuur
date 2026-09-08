<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('plus', fn (?User $user): bool => $user?->isPlus() ?? false);

        if ($this->app->runningInConsole()) {
            return;
        }

        URL::forceRootUrl(rtrim(request()->root(), '/'));
    }
}
