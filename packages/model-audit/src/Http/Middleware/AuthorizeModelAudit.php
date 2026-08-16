<?php

namespace Johannesclimacus\ModelAudit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeModelAudit
{
    public function handle(Request $request, Closure $next): Response
    {
        Gate::authorize(config('model-audit.ui.ability', 'viewModelAudit'));

        return $next($request);
    }
}
