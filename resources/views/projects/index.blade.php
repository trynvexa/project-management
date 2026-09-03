@extends('layouts.app')
@section('title', 'Projects · Flowbase') @section('page_title', 'Projects')
@section('content')
    @php($statusClass = ['Planning' => 'badge-status-todo', 'In Progress' => 'badge-status-progress', 'Review' => 'badge-status-review', 'Completed' => 'badge-status-complete'])
    <div class="app-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold text-indigo-600">Delivery workspace</p>
                <h2 class="mt-1 font-display text-2xl font-extrabold tracking-tight">Projects</h2>
                <p class="mt-1 text-sm text-slate-500">Plan, track, and deliver every client project.</p>
            </div><a href="{{ route('projects.create') }}" class="btn-primary">+ Add project</a>
        </div>
        <section class="panel p-4">
            <form class="grid gap-3 md:grid-cols-4"><input class="form-input" name="search" value="{{ request('search') }}"
                    placeholder="Search projects"><select class="form-input" name="status">
                    <option value="">All statuses</option>
                    @foreach (['Planning', 'In Progress', 'Review', 'Completed'] as $status)
                        <option @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select class="form-input" name="client_id">
                    <option value="">All clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">Apply filters</button>
            </form>
        </section>
        <section class="panel overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th class="min-w-40">Progress</th>
                            <th>Tasks</th>
                            <th>Due date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="stagger">
                        @forelse($projects as $project)
                            <tr>
                                <td><a href="{{ route('projects.show', $project) }}"
                                        class="font-bold text-slate-900 hover:text-indigo-600">{{ $project->name }}</a>
                                    <p class="mt-1 text-xs text-slate-400">{{ $project->type ?? 'Project' }}</p>
                                </td>
                                <td>{{ $project->clientRelation?->name ?? ($project->client ?? '—') }}</td>
                                <td><span
                                        class="badge {{ $statusClass[$project->status] ?? 'badge-status-todo' }}">{{ $project->status ?? 'Planning' }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="progress-track flex-1">
                                            <div class="progress-bar" data-progress="{{ $project->progress }}"></div>
                                        </div><span class="text-xs font-bold">{{ $project->progress }}%</span>
                                    </div>
                                </td>
                                <td>{{ $project->tasks_count }}</td>
                                <td>{{ $project->deadline?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <div class="flex justify-end gap-3 text-sm font-bold"><a class="text-indigo-600"
                                            href="{{ route('projects.show', $project) }}">View</a><a
                                            href="{{ route('projects.edit', $project) }}">Edit</a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                            data-confirm="Delete {{ $project->name }}?">@csrf @method('DELETE')<button
                                                class="text-rose-600">Delete</button></form>
                                    </div>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="7"><x-empty-state message="Projects will appear here when you create them."
                                        action="Create project" :href="route('projects.create')" /></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>{{ $projects->links() }}
    </div>
@endsection
