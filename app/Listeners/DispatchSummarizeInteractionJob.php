<?php

namespace App\Listeners;

use App\Events\InteractionCreated;
use App\Jobs\SummarizeInteractionNotesJob;
use App\Services\TenantManager;

class DispatchSummarizeInteractionJob
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function handle(InteractionCreated $event): void
    {
        $tenant = $this->tenantManager->getTenant();
        if (!$tenant || empty($event->interaction->notes)) {
            return;
        }
        SummarizeInteractionNotesJob::dispatch($tenant->id, $event->interaction->id);
    }
}
