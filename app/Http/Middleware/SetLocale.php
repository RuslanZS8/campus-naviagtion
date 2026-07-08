<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // ─── Skip locale setting for API requests ───
        if ($request->is('api/*')) {
            return $next($request);
        }

        // ─── Check query param first ───
        $locale = $request->query('lang');

        // ─── If not in query, check session ───
        if (!$locale) {
            $locale = session('locale', 'en');
        }

        // ─── Validate and set ───
        if (in_array($locale, ['en', 'tr'])) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
