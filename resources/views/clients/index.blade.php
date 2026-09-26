@extends('layouts.app')
@section('title', 'Clients · Digital Code') @section('page_title', 'Clients')
@section('content')
    <div class="app-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold text-indigo-600">Relationship management</p>
                <h2 class="mt-1 font-display text-2xl font-extrabold tracking-tight">Clients</h2>
                <p class="mt-1 text-sm text-slate-500">Manage the people and businesses behind your work.</p>
            </div><a href="{{ route('clients.create') }}" class="btn-primary">+ Add client</a>
        </div>
        <section class="panel p-4">
            <form action="{{ route('clients') }}" class="flex flex-col gap-3 sm:flex-row"><input class="form-input flex-1"
                    name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone"><button
                    class="btn-secondary">Search</button></form>
            <p class="mt-3 text-xs font-medium text-slate-500">{{ $clients->total() }}
                {{ Str::plural('client', $clients->total()) }} found</p>
        </section>
        <section class="panel overflow-hidden p-0">
            <div class="hidden overflow-x-auto md:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Contact information</th>
                            <th>Projects</th>
                            <th>Tasks</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="stagger">
                        @forelse($clients as $client)
                            <tr>
                                <td><a class="flex items-center gap-3 font-bold text-slate-900 hover:text-indigo-600"
                                        href="{{ route('clients.show', $client) }}"><span
                                            class="avatar h-9 w-9 text-xs">{{ strtoupper(substr($client->name, 0, 2)) }}</span>{{ $client->name }}</a>
                                </td>
                                <td>
                                    <p>{{ $client->email ?? 'No email' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $client->phone ?? 'No phone number' }}</p>
                                </td>
                                <td><span class="badge badge-indigo">{{ $client->projects_count }}</span></td>
                                <td>{{ $client->tasks_count }}</td>
                                <td>
                                    <div class="flex justify-end gap-3 text-sm font-bold"><a class="text-indigo-600"
                                            href="{{ route('clients.show', $client) }}">View</a><a class="text-slate-600"
                                            href="{{ route('clients.edit', $client) }}">Edit</a>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                            data-confirm="Delete {{ $client->name }}? This cannot be undone.">@csrf
                                            @method('DELETE')<button class="text-rose-600">Delete</button></form>
                                    </div>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="5"><x-empty-state
                                        message="Add your first client to start organizing projects." action="Add client"
                                        :href="route('clients.create')" /></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="stagger space-y-3 p-4 md:hidden">
                @forelse($clients as $client)
                    <article
                        class="rounded-2xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:shadow-lg">
                        <div class="flex justify-between gap-3"><a class="flex items-center gap-3 font-bold"
                                href="{{ route('clients.show', $client) }}"><span
                                    class="avatar h-9 w-9 text-xs">{{ strtoupper(substr($client->name, 0, 2)) }}</span>{{ $client->name }}</a><span
                                class="badge badge-indigo">{{ $client->projects_count }} projects</span></div>
                        <p class="mt-3 text-sm text-slate-500">
                            {{ $client->email ?? ($client->phone ?? 'No contact details') }}</p>
                        <div class="mt-4 flex gap-4 text-sm font-bold"><a class="text-indigo-600"
                                href="{{ route('clients.show', $client) }}">View</a><a
                                href="{{ route('clients.edit', $client) }}">Edit</a></div>
                </article>@empty<x-empty-state message="Add your first client to start organizing projects."
                        action="Add client" :href="route('clients.create')" />
                @endforelse
            </div>
        </section>{{ $clients->links() }}
    </div>
@endsection
