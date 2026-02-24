<?php

namespace App\Providers;

use App\Repositories\ContactRepository;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use App\Repositories\InteractionRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(InteractionRepositoryInterface::class, InteractionRepository::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
