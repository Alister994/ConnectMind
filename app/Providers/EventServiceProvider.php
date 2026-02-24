<?php

namespace App\Providers;

use App\Events\InteractionCreated;
use App\Events\TenantCreated;
use App\Listeners\CreateTenantDatabase;
use App\Listeners\DispatchSummarizeInteractionJob;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TenantCreated::class => [
            CreateTenantDatabase::class,
        ],
        InteractionCreated::class => [
            DispatchSummarizeInteractionJob::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
