<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Http\Controllers\WorkspaceManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// AUTH (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:password.reset')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/invitations/{token}', [WorkspaceInvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{token}/accept', [WorkspaceInvitationController::class, 'accept'])->middleware('throttle:invitation.accept')->name('invitations.accept');
    Route::post('/invitations/{token}/decline', [WorkspaceInvitationController::class, 'decline'])->middleware('throttle:invitation.accept')->name('invitations.decline');
});

// LOGOUT (must be logged in)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// EVERYTHING BELOW REQUIRES LOGIN
Route::middleware('auth')->group(function () {
    Route::get('/workspaces/create', [WorkspaceManagementController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceManagementController::class, 'store'])->name('workspaces.store');
    Route::post('/workspaces/{workspace}/switch', [WorkspaceManagementController::class, 'switch'])->name('workspaces.switch');

    Route::middleware('workspace')->group(function () {
        // DASHBOARD
        Route::get('/', function () {
            return redirect('/dashboard');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markUnread'])->name('notifications.unread');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/calendar', [WorkspaceController::class, 'calendar'])->name('calendar');
        Route::get('/reports', [WorkspaceController::class, 'reports'])->name('reports');
        Route::get('/activity', [WorkspaceController::class, 'activity'])->name('activity');
        Route::get('/search', [WorkspaceController::class, 'search'])->name('search');

        // PROFILE
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'updateInfo'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // PROJECTS
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
            ->name('projects.destroy')
            ->middleware('role:owner,admin');

        // TASKS
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
        Route::get('/tasks/board', [TaskController::class, 'board'])->name('tasks.board');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        // CLIENTS
        Route::get('/clients', [ClientController::class, 'index'])->name('clients');
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
            ->name('clients.destroy')
            ->middleware('role:owner,admin');

        // TEAM
        Route::get('/team', [TeamController::class, 'index'])->name('team');
        Route::middleware('role:owner,admin')->group(function () {
            Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
            Route::post('/team', [TeamController::class, 'store'])->name('team.store');
            Route::post('/team/invitations', [WorkspaceInvitationController::class, 'store'])->middleware('throttle:invitation.send')->name('team.invitations.store');
            Route::get('/team/{member}/edit', [TeamController::class, 'edit'])->name('team.edit');
            Route::put('/team/{member}', [TeamController::class, 'update'])->name('team.update');
            Route::delete('/team/{member}', [TeamController::class, 'destroy'])->name('team.destroy');
        });
    });
});
