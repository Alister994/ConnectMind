<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantFromUser
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }
        if (!$user->tenant_id) {
            if ($request->expectsJson()) {
                abort(403, 'No workspace. Please complete registration.');
            }
            return redirect()->route('workspace.setup');
        }
        $tenant = $user->tenant;
        if (!$tenant || !$tenant->is_active) {
            abort(403, 'Tenant not active.');
        }
        $this->tenantManager->setTenant($tenant);
        return $next($request);
    }
}
