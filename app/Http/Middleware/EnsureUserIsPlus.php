<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPlus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPlus()) {
            if ($request->expectsJson()) {
                abort(403, 'Candidakuur Plus vereist.');
            }

            return redirect()
                ->route('plus.index')
                ->with('status', 'Deze functie hoort bij Candidakuur Plus.');
        }

        return $next($request);
    }
}
