@extends('layouts.app')

@section('title', $client->name . ' - Digital Code')
@section('page_title', 'Client profile')

@section('content')

    <div class="app-page max-w-6xl">

        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('clients') }}" class="text-[13px] font-medium text-[#8A8C90] hover:text-[#B8912B]">← Kembali
                    ke Clients</a>
                <h1 class="mt-2 font-['Newsreader'] text-[30px] font-medium text-[#10131A]">{{ $client->name }}</h1>
            </div>
            <a href="{{ route('clients.edit', $client) }}"
                class="rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8912B] px-5 py-2.5 text-[14px] font-semibold text-[#14171F] hover:brightness-105">
                Edit Client
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="rounded-2xl border border-[#E6E2D6] bg-white p-6 lg:col-span-1">
                <h2 class="mb-4 text-[15px] font-semibold text-[#10131A]">Informasi Kontak</h2>
                <dl class="space-y-4 text-[14px]">
                    <div>
                        <dt class="text-[12px] uppercase tracking-wide text-[#8A8C90]">Email</dt>
                        <dd class="mt-1 text-[#10131A]">{{ $client->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] uppercase tracking-wide text-[#8A8C90]">Telepon</dt>
                        <dd class="mt-1 text-[#10131A]">{{ $client->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] uppercase tracking-wide text-[#8A8C90]">Alamat</dt>
                        <dd class="mt-1 text-[#10131A]">{{ $client->address ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-[#E6E2D6] bg-white p-6 lg:col-span-2">
                <h2 class="mb-4 text-[15px] font-semibold text-[#10131A]">Project terkait</h2>

                <div class="mb-4 flex gap-3 text-xs font-medium text-[#6B6D72]"><span class="rounded-full bg-[#F4F3EF] px-3 py-1">{{ $client->projects->count() }} projects</span><span class="rounded-full bg-[#F4F3EF] px-3 py-1">{{ $client->projects->sum(fn ($project) => $project->tasks->count()) }} tasks</span></div>

                @forelse ($client->projects as $project)
                    <div class="flex items-center justify-between border-t border-[#F0EEE6] py-3.5 first:border-t-0">
                        <div class="min-w-0">
                            <a href="{{ route('projects.show', $project) }}"
                                class="truncate text-[14px] font-medium text-[#10131A] hover:text-[#B8912B]">
                                {{ $project->name }}
                            </a>
                            <p class="text-[12px] text-[#8A8C90]">{{ $project->type }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-[#F4F3EF] px-3 py-1 text-[11px] font-medium text-[#6B6D72]">
                            {{ $project->status }}
                        </span>
                    </div>
                @empty
                    <p class="py-6 text-center text-[13px] text-[#8A8C90]">Belum ada project untuk client ini.</p>
                @endforelse
            </div>

        </div>

    </div>

@endsection
