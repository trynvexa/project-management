@extends('layouts.app')
@section('title', 'Dashboard · Digital Code') @section('page_title', 'Workspace overview') @section('breadcrumb',
'Dashboard')
@section('content')
    @php($statusMeta = ['Todo' => ['To do', 'bg-[#E5E7EB]'], 'In Progress' => ['In progress', 'bg-[#DCEAF7]'], 'Review' => ['In review', 'bg-[#F7E9DF]'], 'Completed' => ['Completed', 'bg-[#E4EEE8]']])
    <div class="app-page">
        <section
            class="panel relative overflow-hidden px-6 py-7 sm:px-8 sm:py-9" style="background:rgba(234,243,248,.55)">
            <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/60 blur-3xl"></div>
            <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-semibold" style="color:#7189A6">{{ now()->format('l, d F') }}</p>
                    <h2 class="mt-2 font-display text-2xl font-semibold tracking-tight sm:text-3xl" style="color:#374151">Good to see you,
                        {{ explode(' ', auth()->user()->name)[0] }}.</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6" style="color:#6B7280">A clear view of your projects, priorities, and
                        the work your team is moving forward.</p>
                </div>
                <div class="flex flex-wrap gap-3"><a href="{{ route('tasks.create') }}"
                        class="btn-secondary">New
                        task</a><a href="{{ route('projects.create') }}" class="btn-primary">Create project</a></div>
            </div>
        </section>
        <section class="stagger grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([['Clients', $stats['clients'], 'clients', 'icon-soft-lavender'], ['Projects', $stats['projects'], 'projects', 'icon-soft-blue'], ['Tasks', $stats['tasks'], 'tasks', 'icon-soft-peach'], ['Completed', $stats['tasks_done'], 'tasks', 'icon-soft-sage']] as [$label, $value, $route, $color])
                <a href="{{ route($route) }}" class="metric-card"><span
                        class="grid h-9 w-9 place-items-center rounded-xl {{ $color }} text-sm font-bold">{{ substr($label, 0, 1) }}</span>
                    <p class="mt-4 text-sm font-semibold text-slate-500">{{ $label }}</p>
                    <p class="mt-1 font-display text-3xl font-bold tracking-tight text-slate-900">{{ $value }}
                    </p><span class="mt-3 inline-flex text-xs font-semibold" style="color:#7189A6">View details →</span>
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
                        style="background:conic-gradient(#A9C3D9 {{ round($projectProgress->average ?? 0) * 3.6 }}deg,#EAF3F8 0)">
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
            <section class="panel relative overflow-hidden" style="background:rgba(245,231,234,.55)">
                <p class="text-sm font-semibold" style="color:#A77D87">Attention needed</p>
                <p class="mt-3 font-display text-4xl font-bold" style="color:#374151">{{ $stats['tasks_overdue'] }}</p>
                <p class="mt-1 text-sm" style="color:#6B7280">overdue {{ Str::plural('task', $stats['tasks_overdue']) }}</p><a
                    href="{{ route('tasks') }}" class="mt-8 inline-flex text-sm font-semibold" style="color:#A77D87">Review tasks →</a>
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
                            class="card-project soft-hover group rounded-[20px] border p-4">
                            <div class="flex items-start justify-between gap-3"><span
                                    class="grid h-9 w-9 place-items-center rounded-xl icon-soft-blue font-display text-sm font-bold">{{ strtoupper(substr($project->name, 0, 1)) }}</span><span
                                    class="badge badge-indigo">{{ $project->progress }}%</span></div>
                            <h3
                                class="mt-4 truncate font-display text-sm font-semibold text-slate-900">
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
                                class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $task->isOverdue() ? 'icon-soft-rose' : 'icon-soft-blue' }} text-xs font-bold">{{ $task->deadline?->format('d') }}</span><span
                                class="min-w-0 flex-1"><b
                                    class="block truncate text-sm text-slate-800">{{ $task->name }}</b><small
                                    class="block truncate pt-0.5 text-xs text-slate-500">{{ $task->projectRelation?->name ?? 'No project' }}</small></span><small
                            class="shrink-0 pt-1 text-xs font-semibold {{ $task->isOverdue() ? 'text-[#A77D87]' : 'text-slate-400' }}">{{ $task->deadline?->isToday() ? 'Today' : $task->deadline?->format('d M') }}</small></a>@empty
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
                        class="card-task soft-hover flex items-center gap-3 rounded-[18px] border p-3"><span
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
