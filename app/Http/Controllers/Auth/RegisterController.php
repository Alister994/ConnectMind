<?php

namespace App\Http\Controllers\Auth;

use App\Events\TenantCreated;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $slug = Str::slug($request->name) . '_' . substr(md5($user->id . $user->email), 0, 8);
        $tenant = Tenant::create([
            'name' => $request->name . "'s Workspace",
            'slug' => $slug,
            'database' => '', // set by CreateTenantDatabase listener
            'user_id' => $user->id,
        ]);

        event(new TenantCreated($tenant));

        $user->update(['tenant_id' => $tenant->id]);
        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard');
    }
}
