@extends('layouts.app')
@section('title', 'Dashboard · Flowbase') @section('page_title', 'Workspace overview') @section('breadcrumb',
'Dashboard')
@section('content')
    @php($statusMeta = ['Todo' => ['To do', 'bg-slate-400'], 'In Progress' => ['In progress', 'bg-blue-500'], 'Review' => ['In review', 'bg-amber-500'], 'Completed' => ['Completed', 'bg-emerald-500']])
    <div class="app-page">
        <section
            class="relative overflow-hidden rounded-3xl bg-slate-900 px-6 py-7 text-white shadow-xl shadow-slate-900/15 sm:px-8 sm:py-9">
            <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-indigo-500/25 blur-3xl"></div>
            <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-bold text-indigo-300">{{ now()->format('l, d F') }}</p>
                    <h2 class="mt-2 font-display text-2xl font-extrabold tracking-tight sm:text-3xl">Good to see you,
                        {{ explode(' ', auth()->user()->name)[0] }}.</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-slate-300">A clear view of your projects, priorities, and
                        the work your team is moving forward.</p>
                </div>
                <div class="flex flex-wrap gap-3"><a href="{{ route('tasks.create') }}"
                        class="btn-secondary border-white/15 bg-white/10 text-white hover:bg-white hover:text-slate-900">New
                        task</a><a href="{{ route('projects.create') }}" class="btn-primary">Create project</a></div>
            </div>
        </section>
        <section class="stagger grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([['Clients', $stats['clients'], 'clients', 'bg-violet-50 text-violet-600'], ['Projects', $stats['projects'], 'projects', 'bg-blue-50 text-blue-600'], ['Tasks', $stats['tasks'], 'tasks', 'bg-amber-50 text-amber-600'], ['Completed', $stats['tasks_done'], 'tasks', 'bg-emerald-50 text-emerald-600']] as [$label, $value, $route, $color])
                <a href="{{ route($route) }}" class="metric-card"><span
                        class="grid h-9 w-9 place-items-center rounded-xl {{ $color }} text-sm font-extrabold">{{ substr($label, 0, 1) }}</span>
                    <p class="mt-4 text-sm font-semibold text-slate-500">{{ $label }}</p>
                    <p class="mt-1 font-display text-3xl font-extrabold tracking-tight text-slate-900">{{ $value }}
                    </p><span class="mt-3 inline-flex text-xs font-bold text-indigo-600">View details →</span>
                </a>
            @endforeach
        </section>
        <div class="grid gap-6 xl:grid-cols-3">
            <section class="panel xl:col-span-2">
                <div class="section-head">
                    <div>
                        <h2>Project health</h2>
                        <p>Progress across the projects in your workspace.</p>
                    </div><span class="badge badge-indigo">{{ $projectProgress->total }} total projects</span>
                </div>
                <div class="mt-7 flex flex-col gap-7 sm:flex-row sm:items-center">
                    <div class="relative mx-auto h-32 w-32 shrink-0 rounded-full"
                        style="background:conic-gradient(#6366f1 {{ round($projectProgress->average ?? 0) * 3.6 }}deg,#eef2ff 0)">
                        <div class="absolute inset-2 grid place-items-center rounded-full bg-white"><span
                                class="font-display text-2xl font-extrabold">{{ round($projectProgress->average ?? 0) }}%</span><small
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Average</small></div>
                    </div>
                    <div class="grid flex-1 grid-cols-2 gap-x-5 gap-y-6 sm:grid-cols-4">
                        @foreach ($statusMeta as $status => [$label, $color])
                            <div><span class="inline-block h-2.5 w-2.5 rounded-full {{ $color }}"></span>
                                <p class="mt-2 font-display text-2xl font-extrabold text-slate-900">
                                    {{ $tasksByStatus[$status] ?? 0 }}</p>
                                <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <section class="panel relative overflow-hidden bg-gradient-to-br from-indigo-600 to-violet-700 text-white">
                <p class="text-sm font-bold text-indigo-100">Attention needed</p>
                <p class="mt-3 font-display text-4xl font-extrabold">{{ $stats['tasks_overdue'] }}</p>
                <p class="mt-1 text-sm text-indigo-100">overdue {{ Str::plural('task', $stats['tasks_overdue']) }}</p><a
                    href="{{ route('tasks') }}" class="mt-8 inline-flex text-sm font-bold text-white">Review tasks →</a>
            </section>
        </div>
        <div class="grid gap-6 xl:grid-cols-3">
            <section class="panel xl:col-span-2">
                <div class="section-head">
                    <div>
                        <h2>Recent projects</h2>
                        <p>Your newest client engagements.</p>
                    </div><a href="{{ route('projects') }}">All projects →</a>
                </div>
                <div class="stagger mt-5 grid gap-3 sm:grid-cols-2">
                    @forelse($recentProjects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                            class="group rounded-2xl border border-slate-100 p-4 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-950/5">
                            <div class="flex items-start justify-between gap-3"><span
                                    class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-50 font-display text-sm font-bold text-indigo-600">{{ strtoupper(substr($project->name, 0, 1)) }}</span><span
                                    class="badge badge-indigo">{{ $project->progress }}%</span></div>
                            <h3
                                class="mt-4 truncate font-display text-sm font-bold text-slate-900 group-hover:text-indigo-700">
                                {{ $project->name }}</h3>
                            <p class="mt-1 truncate text-xs text-slate-500">
                                {{ $project->clientRelation?->name ?? ($project->client ?? 'No client') }}</p>
                            <div class="mt-4 progress-track">
                                <div class="progress-bar" data-progress="{{ $project->progress }}"></div>
                            </div>
                            <div class="mt-3 flex justify-between text-xs text-slate-500">
                                <span>{{ $project->status ?? 'Planning' }}</span><span>{{ $project->deadline?->format('d M') ?? 'No deadline' }}</span>
                            </div>
                    </a>@empty<x-empty-state message="Create a project to see its progress here."
                            action="Create project" :href="route('projects.create')" />
                    @endforelse
                </div>
            </section>
            <section class="panel">
                <div class="section-head">
                    <div>
                        <h2>Upcoming deadlines</h2>
                        <p>Tasks due soon.</p>
                    </div><a href="{{ route('tasks') }}">View all</a>
                </div>
                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($upcomingTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}"
                            class="flex gap-3 py-3 transition hover:translate-x-0.5"><span
                                class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $task->isOverdue() ? 'bg-rose-50 text-rose-600' : 'bg-indigo-50 text-indigo-600' }} text-xs font-extrabold">{{ $task->deadline?->format('d') }}</span><span
                                class="min-w-0 flex-1"><b
                                    class="block truncate text-sm text-slate-800">{{ $task->name }}</b><small
                                    class="block truncate pt-0.5 text-xs text-slate-500">{{ $task->projectRelation?->name ?? 'No project' }}</small></span><small
                            class="shrink-0 pt-1 text-xs font-bold {{ $task->isOverdue() ? 'text-rose-600' : 'text-slate-400' }}">{{ $task->deadline?->isToday() ? 'Today' : $task->deadline?->format('d M') }}</small></a>@empty
                        <p class="py-8 text-center text-sm text-slate-400">No upcoming deadlines.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <section class="panel">
            <div class="section-head">
                <div>
                    <h2>Latest task activity</h2>
                    <p>Recently created work across the workspace.</p>
                </div><a href="{{ route('tasks') }}">See tasks →</a>
            </div>
            <div class="stagger mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($recentActivity as $task)
                    <a href="{{ route('tasks.show', $task) }}"
                        class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-indigo-200 hover:bg-indigo-50/30"><span
                            class="avatar h-9 w-9 text-xs">{{ strtoupper(substr($task->assignee?->name ?? '?', 0, 1)) }}</span><span
                            class="min-w-0"><b class="block truncate text-sm text-slate-800">{{ $task->name }}</b><small
                                class="block truncate text-xs text-slate-500">{{ $task->projectRelation?->name ?? 'No project' }}
                            · {{ $task->created_at->diffForHumans() }}</small></span></a>@empty<p
                        class="text-sm text-slate-400">No activity yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
