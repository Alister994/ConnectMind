<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    public function setTenant(Tenant $tenant): void
    {
        $this->currentTenant = $tenant;
        Config::set('database.connections.tenant.database', $tenant->database);
        DB::purge('tenant');
    }

    public function getTenant(): ?Tenant
    {
        return $this->currentTenant;
    }

    public function clearTenant(): void
    {
        $this->currentTenant = null;
    }

    public function createTenantDatabase(string $databaseName): bool
    {
        $connectionName = config('tenancy.database_connection', 'mysql');
        $connection = DB::connection($connectionName);
        $driver = $connection->getDriverName();

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('Tenant database creation requires MySQL or MariaDB. Set DB_CONNECTION=mysql and configure MySQL in .env.');
        }

        $config = config("database.connections.{$connectionName}");
        $charset = $config['charset'] ?? 'utf8mb4';
        $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

        return $connection->statement(
            "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation}"
        );
    }

    public function runTenantMigrations(Tenant $tenant): void
    {
        $this->setTenant($tenant);
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
        $this->clearTenant();
    }

    public function makeTenantDatabaseName(string $slug): string
    {
        $prefix = config('tenancy.database_prefix', 'connectmind_tenant_');
        return $prefix . Str::slug($slug, '_');
    }
}
