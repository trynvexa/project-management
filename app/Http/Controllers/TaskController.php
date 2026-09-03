<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\WorkspaceMember;
use App\Notifications\TaskWorkspaceNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Menampilkan semua task.
     */
    public function index(Request $request)
    {
        $query = Task::with(['projectRelation', 'assignee']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('project', 'like', "%{$search}%")
                    ->orWhere('member', 'like', "%{$search}%");
            });
        }

        // Filter (poin 8: Search & Filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        $tasks = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $users = $request->user()->workspaces()->find(session('current_workspace_id'))->users()->orderBy('name')->get(['users.id', 'users.name']);

        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    /**
     * Menampilkan Kanban board (dikelompokkan per status).
     */
    public function board()
    {
        $statuses = ['Todo', 'In Progress', 'Review', 'Completed'];

        $tasksByStatus = collect($statuses)->mapWithKeys(function ($status) {
            return [
                $status => Task::with(['projectRelation', 'assignee'])
                    ->where('status', $status)
                    ->latest()
                    ->get(),
            ];
        });

        $tasksByStatus['Completed'] = Task::with(['projectRelation', 'assignee'])
            ->whereIn('status', ['Completed', 'Done'])
            ->latest()
            ->get();

        return view('tasks.kanban', compact('tasksByStatus', 'statuses'));
    }

    /**
     * Update status task saja (dipanggil dari drag & drop di Kanban board).
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Todo,In Progress,Review,Completed'],
        ]);

        $oldStatus = $task->status;
        $task->update($validated);
        $task->load('assignee');

        if ($task->assignee && $task->status !== $oldStatus) {
            $task->assignee->notify(new TaskWorkspaceNotification(
                $task,
                'status_changed',
                "{$task->name} is now {$task->status}."
            ));
        }

        // Progress project otomatis dihitung ulang tiap kali status task berubah
        if ($task->project_id) {
            $task->projectRelation->recalculateProgress();
        }

        return response()->json([
            'success' => true,
            'status' => $task->status,
        ]);
    }

    /**
     * Menampilkan form tambah task.
     */
    public function create()
    {
        $projects = Project::orderBy('name')->get();
        $users = request()->user()->workspaces()->find(session('current_workspace_id'))->users()->orderBy('name')->get();

        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Menyimpan task baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTask($request);

        $task = Task::create($validated);

        $task->load('assignee');
        if ($task->assignee) {
            $task->assignee->notify(new TaskWorkspaceNotification(
                $task,
                'assigned',
                "You were assigned to {$task->name}."
            ));
        }

        if ($task->project_id) {
            $task->projectRelation->recalculateProgress();
        }

        return redirect()
            ->route('tasks')
            ->with('success', 'Task berhasil dibuat.');
    }

    /**
     * Menampilkan form edit task.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $users = request()->user()->workspaces()->find(session('current_workspace_id'))->users()->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function show(Task $task)
    {
        $task->load(['projectRelation.clientRelation', 'assignee']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Mengupdate task.
     */
    public function update(Request $request, Task $task)
    {
        $oldProjectId = $task->project_id;
        $oldAssigneeId = $task->assignee_id;
        $oldStatus = $task->status;

        $validated = $this->validateTask($request);

        $task->update($validated);
        $task->load('assignee');

        if ($task->assignee && $task->assignee_id !== $oldAssigneeId) {
            $task->assignee->notify(new TaskWorkspaceNotification(
                $task,
                'assigned',
                "You were assigned to {$task->name}."
            ));
        }

        if ($task->assignee && $task->status !== $oldStatus) {
            $task->assignee->notify(new TaskWorkspaceNotification(
                $task,
                'status_changed',
                "{$task->name} is now {$task->status}."
            ));
        }

        // Recalculate progress project lama (kalau project-nya dipindah) & project baru
        if ($oldProjectId && $oldProjectId !== $task->project_id) {
            Project::query()->find($oldProjectId)?->recalculateProgress();
        }

        if ($task->project_id) {
            $task->projectRelation->recalculateProgress();
        }

        return redirect()
            ->route('tasks')
            ->with('success', 'Task berhasil diperbarui.');
    }

    /**
     * Menghapus task.
     */
    public function destroy(Task $task)
    {
        $projectId = $task->project_id;

        $task->delete();

        if ($projectId) {
            Project::query()->find($projectId)?->recalculateProgress();
        }

        return redirect()
            ->route('tasks')
            ->with('success', 'Task berhasil dihapus.');
    }

    /**
     * Aturan validasi bersama untuk store & update.
     */
    private function validateTask(Request $request): array
    {
        $validated = $request->validate([
            'project_id' => ['nullable', Rule::exists('projects', 'id')->where('workspace_id', session('current_workspace_id'))],
            'assignee_id' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && ! WorkspaceMember::where('workspace_id', session('current_workspace_id'))->where('user_id', $value)->where('status', 'active')->exists()) {
                    $fail('The selected assignee is not a workspace member.');
                }
            }],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:High,Medium,Low'],
            'status' => ['required', 'in:Todo,In Progress,Review,Completed,Done'],
        ]);

        // Kolom 'project' & 'member' (teks lama) tetap diisi otomatis,
        // biar tampilan lama yang masih pakai kolom ini tetap jalan normal.
        $validated['project'] = ! empty($validated['project_id'])
            ? Project::query()->find($validated['project_id'])?->name
            : null;

        $validated['member'] = ! empty($validated['assignee_id'])
            ? WorkspaceMember::where('workspace_id', session('current_workspace_id'))->where('user_id', $validated['assignee_id'])->with('user')->first()?->user?->name
            : null;

        return $validated;
    }
}
