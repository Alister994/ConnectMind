<?php

namespace App\Listeners;

use App\Events\TenantCreated;
use App\Services\TenantManager;

class CreateTenantDatabase
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;
        $dbName = $this->tenantManager->makeTenantDatabaseName($tenant->slug);
        $tenant->update(['database' => $dbName]);
        $this->tenantManager->createTenantDatabase($dbName);
        $this->tenantManager->runTenantMigrations($tenant);
    }
}
