@extends('layouts.app')

@section('title', 'Tambah Project - Digital Code')
@section('page_title', 'Add project')

@section('content')

    <div class="app-page max-w-4xl">

        <div class="mb-8">
            <a href="{{ route('projects') }}" class="text-[13px] font-medium text-[#8A8C90] hover:text-[#B8912B]">← Kembali ke
                Projects</a>
            <h1 class="mt-2 font-['Newsreader'] text-[30px] font-medium text-[#10131A]">Tambah Project</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 border-l-2 border-red-500 bg-red-500/5 px-4 py-3.5 text-[14px] text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="max-w-2xl rounded-2xl border border-[#E6E2D6] bg-white p-7">
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Nama Project <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                    </div>

                    <x-client-autocomplete :clients="$clients" :selected-id="old('client_id')" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Tipe</label>
                        <select name="type"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            @foreach (['Website', 'Mobile App', 'Software', 'Graphic Design'] as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Status</label>
                        <select name="status"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            @foreach (['Planning', 'In Progress', 'Review', 'Completed'] as $status)
                                <option value="{{ $status }}" @selected(old('status') === $status)>{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Deadline <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" required
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Progress (%)</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ old('progress', 0) }}"
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Deskripsi</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8912B] px-6 py-2.5 text-[14px] font-semibold text-[#14171F] hover:brightness-105">
                        Simpan Project
                    </button>
                    <a href="{{ route('projects') }}"
                        class="rounded-lg px-6 py-2.5 text-[14px] font-medium text-[#6B6D72] hover:bg-[#F4F3EF]">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection
