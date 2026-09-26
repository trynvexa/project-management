@extends('layouts.app')
@section('title', 'Tasks · Digital Code') @section('page_title', 'Tasks')
@section('content')
    @php($statusClass = ['Todo' => 'badge-status-todo', 'In Progress' => 'badge-status-progress', 'Review' => 'badge-status-review', 'Completed' => 'badge-status-complete', 'Done' => 'badge-status-complete']) @php($priorityClass = ['High' => 'badge-priority-high', 'Medium' => 'badge-priority-medium', 'Low' => 'badge-priority-low', 'Urgent' => 'badge-priority-high'])
    <div class="app-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Tasks</h2>
                <p class="mt-1 text-sm text-slate-500">Keep work moving with a clear view of every task.</p>
            </div>
            <div class="flex gap-3"><a href="{{ route('tasks.board') }}" class="btn-secondary">Kanban board</a><a
                    href="{{ route('tasks.create') }}" class="btn-primary">+ Add task</a></div>
        </div>
        <section class="panel p-4">
            <form class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6"><input class="form-input xl:col-span-1" name="search"
                    value="{{ request('search') }}" placeholder="Search tasks"><select class="form-input" name="project_id">
                    <option value="">All projects</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
                <select class="form-input" name="status">
                    <option value="">All statuses</option>
                    @foreach (['Todo', 'In Progress', 'Review', 'Completed', 'Done'] as $status)
                        <option @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select class="form-input" name="priority">
                    <option value="">All priorities</option>
                    @foreach (['High', 'Medium', 'Low'] as $priority)
                        <option @selected(request('priority') === $priority)>{{ $priority }}</option>
                    @endforeach
                </select>
                <select class="form-input" name="assignee_id">
                    <option value="">All assignees</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assignee_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">Filter</button>
            </form>
        </section>
        <section class="panel overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Project</th>
                            <th>Assignee</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td><a class="font-semibold text-slate-900 hover:text-indigo-600"
                                        href="{{ route('tasks.show', $task) }}">{{ $task->name }}</a></td>
                                <td>{{ $task->projectRelation?->name ?? ($task->project ?? '—') }}</td>
                                <td>
                                    <div class="flex items-center gap-2"><span
                                            class="avatar h-7 w-7 text-[10px]">{{ strtoupper(substr($task->assignee?->name ?? '?', 0, 1)) }}</span>{{ $task->assignee?->name ?? ($task->member ?? 'Unassigned') }}
                                    </div>
                                </td>
                                <td><span
                                        class="badge {{ $priorityClass[$task->priority] ?? 'badge-priority-low' }}">{{ $task->priority }}</span>
                                </td>
                                <td><span
                                        class="badge {{ $statusClass[$task->status] ?? 'badge-status-todo' }}">{{ $task->status }}</span>
                                </td>
                                <td class="{{ $task->isOverdue() ? 'font-semibold text-rose-600' : '' }}">
                                    {{ $task->deadline?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <div class="flex justify-end gap-3 text-sm font-semibold"><a class="text-indigo-600"
                                            href="{{ route('tasks.show', $task) }}">View</a><a
                                            href="{{ route('tasks.edit', $task) }}">Edit</a>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                            data-confirm="Delete {{ $task->name }}?">@csrf @method('DELETE')<button
                                                class="text-rose-600">Delete</button></form>
                                    </div>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="7"><x-empty-state message="Create a task to begin organizing work."
                                        action="Add task" :href="route('tasks.create')" /></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>{{ $tasks->links() }}
    </div>
@endsection
