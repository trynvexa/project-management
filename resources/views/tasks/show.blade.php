@extends('layouts.app')

@section('title', $task->name . ' · Task')
@section('page_title', 'Task details')

@section('content')
    @php
        $statusColors = [
            'Todo' => 'bg-slate-100 text-slate-700',
            'In Progress' => 'bg-blue-50 text-blue-700',
            'Review' => 'bg-amber-50 text-amber-700',
            'Completed' => 'bg-emerald-50 text-emerald-700',
            'Done' => 'bg-emerald-50 text-emerald-700',
        ];
        $priorityColors = [
            'High' => 'bg-rose-50 text-rose-700',
            'Medium' => 'bg-amber-50 text-amber-700',
            'Low' => 'bg-slate-100 text-slate-700',
        ];
    @endphp
    <div class="app-page max-w-6xl">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div><a href="{{ route('tasks') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">← Back to
                    tasks</a>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $task->name }}</h1><span
                        class="badge {{ $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $task->status }}</span>
                </div>
            </div>
            <div class="flex gap-3"><a href="{{ route('tasks.edit', $task) }}" class="btn-primary">Edit task</a></div>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <section class="panel lg:col-span-2">
                <h2 class="text-base font-semibold text-slate-900">Description</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                    {{ $task->description ?: 'No description has been added for this task.' }}</p>
            </section>
            <aside class="panel">
                <h2 class="text-base font-semibold text-slate-900">Task details</h2>
                <dl class="mt-5 space-y-5 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Project</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $task->projectRelation?->name ?? 'No project' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Assignee</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $task->assignee?->name ?? 'Unassigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Due date</dt>
                        <dd class="mt-1 font-semibold text-slate-800">
                            {{ $task->deadline?->format('d M Y') ?? 'No due date' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Priority</dt>
                        <dd class="mt-1"><span
                                class="badge {{ $priorityColors[$task->priority] }}">{{ $task->priority }}</span></dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>
@endsection
