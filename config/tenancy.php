<?php

return [
    'database_prefix' => env('TENANT_DB_PREFIX', 'connectmind_tenant_'),

    /*
    | Connection used to run CREATE DATABASE for new tenants.
    | Must be MySQL/MariaDB. Landlord and tenant DBs use the same server.
    */
    'database_connection' => env('TENANT_DB_CONNECTION', 'mysql'),
];
