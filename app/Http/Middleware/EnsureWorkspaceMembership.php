<?php

namespace App\Http\Middleware;

use App\Support\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! CurrentWorkspace::for($request)) {
            return redirect()->route('workspaces.create');
        }

        return $next($request);
    }
}
