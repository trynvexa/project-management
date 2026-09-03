<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkspaceMember;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'projects' => Project::count(),
            'tasks' => Task::count(),
            'clients' => Client::count(),
            'team' => WorkspaceMember::where('workspace_id', session('current_workspace_id'))->count(),
            'tasks_done' => Task::whereIn('status', ['Completed', 'Done'])->count(),
            'tasks_overdue' => Task::whereNotNull('deadline')
                ->whereDate('deadline', '<', now())
                ->whereNotIn('status', ['Completed', 'Done'])
                ->count(),
        ];

        $projectProgress = Project::selectRaw('AVG(progress) as average, COUNT(*) as total')->first();
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $recentProjects = Project::with('clientRelation')->latest()->take(5)->get();
        $upcomingTasks = Task::whereNotNull('deadline')
            ->whereNotIn('status', ['Completed', 'Done'])
            ->with(['projectRelation', 'assignee'])
            ->orderBy('deadline')
            ->take(5)
            ->get();
        $recentActivity = Task::with(['projectRelation', 'assignee'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'projectProgress', 'tasksByStatus', 'recentProjects', 'upcomingTasks', 'recentActivity'
        ));
    }
}
