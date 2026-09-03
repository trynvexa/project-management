<?php

namespace App\Providers;

use App\Support\WorkspaceNotifications;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('password.reset', fn ($request) => Limit::perMinute(3)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
        RateLimiter::for('login', fn ($request) => Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
        RateLimiter::for('invitation.send', fn ($request) => Limit::perMinute(5)->by((string) $request->user()?->id.'|'.$request->ip()));
        RateLimiter::for('invitation.accept', fn ($request) => Limit::perMinute(10)->by((string) $request->user()?->id.'|'.$request->ip()));
        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $notifications = $user
                ? WorkspaceNotifications::forWorkspace($user->unreadNotifications()->latest()->get(), (int) session('current_workspace_id'))
                : collect();
            $view->with('unreadNotifications', $notifications->count())
                ->with('notificationPreview', $notifications->take(4))
                ->with('workspaceOptions', $user ? $user->workspaces()->wherePivot('status', 'active')->orderBy('workspaces.name')->get() : collect());
        });
    }
}
