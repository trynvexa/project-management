<?php

namespace App\Http\Controllers;

use App\Support\WorkspaceNotifications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->notifications()->latest();

        if ($request->string('filter')->value() === 'unread') {
            $query->whereNull('read_at');
        }

        if ($request->filled('category') && in_array($request->category, ['tasks', 'projects', 'system'], true)) {
            $query->where('data->category', $request->category);
        }

        $notifications = $this->workspaceNotifications($request, $query->get());

        return view('notifications.index', [
            'notifications' => $this->paginate($notifications, $request),
            'unreadCount' => $this->workspaceNotifications($request, $request->user()->unreadNotifications()->get())->count(),
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $this->notification($request, $notification)->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markUnread(Request $request, string $notification): RedirectResponse
    {
        $this->notification($request, $notification)->markAsUnread();

        return back()->with('success', 'Notification marked as unread.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->workspaceNotifications($request, $request->user()->unreadNotifications()->get())
            ->each->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $notification): RedirectResponse
    {
        $this->notification($request, $notification)->delete();

        return back()->with('success', 'Notification deleted.');
    }

    private function notification(Request $request, string $id): DatabaseNotification
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        abort_unless(WorkspaceNotifications::belongsToWorkspace($notification, (int) session('current_workspace_id')), 404);

        return $notification;
    }

    private function workspaceNotifications(Request $request, Collection $notifications): Collection
    {
        return WorkspaceNotifications::forWorkspace($notifications, (int) session('current_workspace_id'));
    }

    private function paginate(Collection $notifications, Request $request): LengthAwarePaginator
    {
        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $notifications->forPage($page, $perPage)->values(),
            $notifications->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
