<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class OpenAICostGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app(TenantManager::class)->getTenant();
        if (!$tenant) {
            return $next($request);
        }

        $key = 'openai_tokens_used_' . $tenant->id . '_' . now()->format('Y-m-d');
        $used = (int) Cache::get($key, 0);
        $limit = (int) config('services.openai.max_daily_tokens_per_tenant', 50000);

        if ($used >= $limit) {
            return response()->json([
                'message' => 'Daily AI usage limit reached. Try again tomorrow.',
            ], 429);
        }

        $response = $next($request);

        if ($request->hasHeader('X-OpenAI-Tokens-Used')) {
            $tokens = (int) $request->header('X-OpenAI-Tokens-Used');
            Cache::put($key, $used + $tokens, now()->endOfDay());
        }

        return $response;
    }
}
