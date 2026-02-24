<?php

namespace App\Http\Controllers;

use App\Events\TenantCreated;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkspaceSetupController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();
        if ($user->tenant_id) {
            return redirect()->route('dashboard');
        }
        return view('workspace.setup');
    }

    public function store(): RedirectResponse
    {
        $user = auth()->user();
        if ($user->tenant_id) {
            return redirect()->route('dashboard');
        }

        // User may already have a tenant (e.g. from a previous failed setup) — attach it
        $tenant = Tenant::where('user_id', $user->id)->first();
        if ($tenant) {
            $user->update(['tenant_id' => $tenant->id]);
            return redirect()->route('dashboard')->with('status', 'Workspace linked successfully.');
        }

        $baseSlug = Str::slug($user->name) . '_' . substr(md5($user->id . $user->email), 0, 8);
        $slug = $baseSlug;
        $attempt = 0;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '_' . Str::random(4);
            if (++$attempt > 10) {
                $slug = $baseSlug . '_' . time();
                break;
            }
        }

        $tenant = Tenant::create([
            'name' => $user->name . "'s Workspace",
            'slug' => $slug,
            'database' => '',
            'user_id' => $user->id,
        ]);

        event(new TenantCreated($tenant));
        $user->update(['tenant_id' => $tenant->id]);

        return redirect()->route('dashboard')->with('status', 'Workspace created successfully.');
    }
}
