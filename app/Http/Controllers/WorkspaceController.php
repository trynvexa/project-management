<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function calendar()
    {
        $tasks = Task::with('projectRelation')->whereNotNull('deadline')->orderBy('deadline')->get();
        $projects = Project::with('clientRelation')->whereNotNull('deadline')->orderBy('deadline')->get();

        return view('workspace.calendar', compact('tasks', 'projects'));
    }

    public function reports()
    {
        $status = Task::selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total', 'status');
        $priority = Task::selectRaw('priority, COUNT(*) total')->groupBy('priority')->pluck('total', 'priority');
        $projects = Project::select('name', 'progress')->latest()->take(8)->get();
        $totalTasks = Task::count();
        $completed = Task::whereIn('status', ['Completed', 'Done'])->count();
        $overdue = Task::whereNotNull('deadline')->whereDate('deadline', '<', now())->whereNotIn('status', ['Completed', 'Done'])->count();

        return view('workspace.reports', compact('status', 'priority', 'projects', 'totalTasks', 'completed', 'overdue'));
    }

    public function activity()
    {
        $tasks = Task::with(['projectRelation', 'assignee'])->latest()->paginate(20);

        return view('workspace.activity', compact('tasks'));
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->query('q'));
        $clients = $term === '' ? collect() : Client::where('name', 'like', "%{$term}%")->limit(6)->get();
        $projects = $term === '' ? collect() : Project::where('name', 'like', "%{$term}%")->limit(6)->get();
        $tasks = $term === '' ? collect() : Task::where('name', 'like', "%{$term}%")->limit(6)->get();

        return view('workspace.search', compact('term', 'clients', 'projects', 'tasks'));
    }
}
