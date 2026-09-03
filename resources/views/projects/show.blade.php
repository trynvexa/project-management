@extends('layouts.app')

@section('title', $project->name . ' - Digital Code')
@section('page_title', 'Project details')

@section('content')

    @php
        $statusColor = [
            'Planning' => 'bg-[#F4F3EF] text-[#6B6D72]',
            'In Progress' => 'bg-[#E3F2F1] text-[#045C5C]',
            'Review' => 'bg-[#FBF1DC] text-[#8A6A16]',
            'Completed' => 'bg-emerald-50 text-emerald-700',
        ];
        $priorityColor = [
            'High' => 'bg-red-50 text-red-600',
            'Medium' => 'bg-[#FBF1DC] text-[#8A6A16]',
            'Low' => 'bg-[#F4F3EF] text-[#6B6D72]',
        ];
    @endphp

    <div class="app-page max-w-7xl">

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('projects') }}" class="text-[13px] font-medium text-[#8A8C90] hover:text-[#B8912B]">←
                    Kembali ke Projects</a>
                <div class="mt-2 flex items-center gap-3">
                    <h1 class="font-['Newsreader'] text-[30px] font-medium text-[#10131A]">{{ $project->name }}</h1>
                    <span
                        class="rounded-full px-3 py-1 text-[11px] font-medium {{ $statusColor[$project->status] ?? 'bg-[#F4F3EF] text-[#6B6D72]' }}">
                        {{ $project->status }}
                    </span>
                </div>
                <p class="mt-1 text-[14px] text-[#6B6D72]">{{ $project->client }} · {{ $project->type }}</p>
            </div>
            <a href="{{ route('projects.edit', $project) }}"
                class="rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8912B] px-5 py-2.5 text-[14px] font-semibold text-[#14171F] hover:brightness-105">
                Edit Project
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4"><div class="metric-card"><p class="text-xs text-slate-500">Total tasks</p><p class="mt-2 text-2xl font-bold">{{ $project->tasks->count() }}</p></div><div class="metric-card"><p class="text-xs text-slate-500">Completed</p><p class="mt-2 text-2xl font-bold">{{ $project->tasks->whereIn('status',['Completed','Done'])->count() }}</p></div><div class="metric-card"><p class="text-xs text-slate-500">In progress</p><p class="mt-2 text-2xl font-bold">{{ $project->tasks->where('status','In Progress')->count() }}</p></div><div class="metric-card"><p class="text-xs text-slate-500">In review</p><p class="mt-2 text-2xl font-bold">{{ $project->tasks->where('status','Review')->count() }}</p></div></div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-1">

                <div class="rounded-2xl border border-[#E6E2D6] bg-white p-6">
                    <p class="mb-1.5 flex items-center justify-between text-[12px] text-[#8A8C90]">
                        <span>Progress</span>
                        <span>{{ $project->progress }}%</span>
                    </p>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#F0EEE6]">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#D4AF37] to-[#B8912B]"
                            style="width: {{ $project->progress }}%"></div>
                    </div>

                    <dl class="mt-6 space-y-4 text-[14px]">
                        <div>
                            <dt class="text-[12px] uppercase tracking-wide text-[#8A8C90]">Mulai</dt>
                            <dd class="mt-1 text-[#10131A]">
                                {{ $project->start_date ? \Illuminate\Support\Carbon::parse($project->start_date)->format('d M Y') : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[12px] uppercase tracking-wide text-[#8A8C90]">Deadline</dt>
                            <dd class="mt-1 text-[#10131A]">
                                {{ $project->deadline ? \Illuminate\Support\Carbon::parse($project->deadline)->format('d M Y') : '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-[#E6E2D6] bg-white p-6">
                    <h2 class="mb-3 text-[15px] font-semibold text-[#10131A]">Deskripsi</h2>
                    <p class="text-[14px] leading-relaxed text-[#6B6D72]">
                        {{ $project->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>

            </div>

            <div class="rounded-2xl border border-[#E6E2D6] bg-white p-6 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-[15px] font-semibold text-[#10131A]">Task pada project ini</h2>
                    <a href="{{ route('tasks.create') }}"
                        class="text-[12px] font-semibold text-[#B8912B] hover:underline">+ Tambah task</a>
                </div>

                @forelse ($project->tasks as $task)
                    <div class="flex items-center justify-between border-t border-[#F0EEE6] py-3.5 first:border-t-0">
                        <div class="min-w-0">
                            <p class="truncate text-[14px] font-medium text-[#10131A]">{{ $task->name }}</p>
                            <p class="text-[12px] text-[#8A8C90]">{{ $task->member ?? 'Belum di-assign' }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <span
                                class="rounded-full px-3 py-1 text-[11px] font-medium {{ $priorityColor[$task->priority] ?? 'bg-[#F4F3EF] text-[#6B6D72]' }}">
                                {{ $task->priority }}
                            </span>
                            <span class="rounded-full bg-[#F4F3EF] px-3 py-1 text-[11px] font-medium text-[#6B6D72]">
                                {{ $task->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-[13px] text-[#8A8C90]">Belum ada task untuk project ini.</p>
                @endforelse
            </div>

        </div>

    </div>

@endsection
