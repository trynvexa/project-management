<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Contoh pakai di route: ->middleware('role:admin,manager')
     * Artinya hanya user dengan role admin ATAU manager yang boleh akses.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $workspaceId = $request->session()->get('current_workspace_id');
        $role = $workspaceId ? $user?->workspaceMemberships()
            ->where('workspace_id', $workspaceId)->where('status', 'active')->value('role') : null;

        // Legacy manager is accepted as admin only while migrating existing UI.
        $allowed = collect($roles)->map(fn ($role) => $role === 'manager' ? 'admin' : $role)->all();
        if (! $user || ! in_array($role, $allowed, true)) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
