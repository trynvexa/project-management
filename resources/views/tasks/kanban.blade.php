@extends('layouts.app')
@section('title', 'Kanban · Flowbase') @section('page_title', 'Kanban board')
@section('content')
    @php($meta = ['Todo' => ['badge-status-todo', 'To do'], 'In Progress' => ['badge-status-progress', 'In progress'], 'Review' => ['badge-status-review', 'Review'], 'Completed' => ['badge-status-complete', 'Completed']]) @php($priority = ['High' => 'badge-priority-high', 'Medium' => 'badge-priority-medium', 'Low' => 'badge-priority-low'])
    <div class="app-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold text-indigo-600">Visual workflow</p>
                <h2 class="mt-1 font-display text-2xl font-extrabold tracking-tight">Kanban board</h2>
                <p class="mt-1 text-sm text-slate-500">Drag tasks between stages to update progress instantly.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('tasks') }}" class="btn-secondary">List view</a>
                <a href="{{ route('tasks.create') }}" class="btn-primary">+ Add task</a>
            </div>
        </div>
        <div class="kanban-board grid gap-5 md:grid-cols-2 2xl:grid-cols-4">
            @foreach ($statuses as $status)
                <section class="rounded-2xl border border-slate-200 bg-slate-100/70 p-3">
                    <header class="mb-3 flex items-center justify-between px-1">
                        <h3 class="font-display text-sm font-bold text-slate-800">{{ $meta[$status][1] }}</h3>
                        <span class="badge {{ $meta[$status][0] }}" data-count>{{ $tasksByStatus[$status]->count() }}</span>
                    </header>
                    <div class="kanban-column min-h-36 space-y-3" data-status="{{ $status }}">
                        @forelse($tasksByStatus[$status] as $task)
                            <article draggable="true" data-id="{{ $task->id }}"
                                class="kanban-card cursor-grab rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md active:cursor-grabbing">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ route('tasks.show', $task) }}"
                                        class="text-sm font-bold leading-5 text-slate-800">{{ $task->name }}</a>
                                    <span
                                        class="badge {{ $priority[$task->priority] ?? 'badge-priority-low' }}">{{ $task->priority }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $task->projectRelation?->name ?? ($task->project ?? 'No project') }}</p>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span
                                            class="avatar h-6 w-6 text-[9px]">{{ strtoupper(substr($task->assignee?->name ?? '?', 0, 1)) }}</span>{{ $task->assignee?->name ?? 'Unassigned' }}
                                    </div>
                                    <span
                                        class="text-xs {{ $task->isOverdue() ? 'font-semibold text-rose-600' : 'text-slate-400' }}">{{ $task->deadline?->format('d M') ?? 'No date' }}</span>
                                </div>
                        </article>@empty<div
                                class="rounded-xl border border-dashed border-slate-300 bg-white/60 p-5 text-center text-xs text-slate-400">
                                Drop tasks here</div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').content,
                cols = document.querySelectorAll('.kanban-column'),
                counts = () => cols.forEach(c => c.closest('section').querySelector('[data-count]').textContent = c
                    .querySelectorAll('.kanban-card').length);
            cols.forEach(col => new window.Sortable(col, {
                group: 'flowbase-tasks',
                animation: 170,
                ghostClass: 'dragging',
                chosenClass: 'kanban-over',
                dragClass: 'dragging',
                onEnd: async e => {
                    if (e.from === e.to) {
                        counts();
                        return
                    }
                    const task = e.item,
                        old = e.from,
                        status = e.to.dataset.status;
                    counts();
                    task.style.pointerEvents = 'none';
                    try {
                        const r = await fetch(`/tasks/${task.dataset.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({
                                status
                            })
                        });
                        if (!r.ok) throw Error();
                        window.Flowbase?.toast('Task status updated successfully.', 'success')
                    } catch {
                        old.append(task);
                        counts();
                        window.Flowbase?.toast(
                            'Status could not be updated. Changes were rolled back.', 'error')
                    } finally {
                        task.style.pointerEvents = ''
                    }
                }
            }))
        })
    </script>
@endsection
