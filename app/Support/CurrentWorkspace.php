<?php

namespace App\Support;

use App\Models\Workspace;
use Illuminate\Http\Request;

class CurrentWorkspace
{
    public const SESSION_KEY = 'current_workspace_id';

    public static function for(Request $request): ?Workspace
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $id = $request->session()->get(self::SESSION_KEY);
        $workspace = $id ? $user->workspaces()->whereKey($id)->wherePivot('status', 'active')->first() : null;
        if (! $workspace) {
            $workspace = $user->workspaces()->wherePivot('status', 'active')->orderBy('workspaces.id')->first();
            if ($workspace) {
                $request->session()->put(self::SESSION_KEY, $workspace->id);
            }
        }

        return $workspace;
    }
}
