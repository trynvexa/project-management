<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSessionManager
{
    public static function invalidateAll(User $user, ?Request $except = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }
        $query = DB::table(config('session.table'))->where('user_id', $user->id);
        if ($except?->session()->getId()) {
            $query->where('id', '!=', $except->session()->getId());
        }
        $query->delete();
    }
}
