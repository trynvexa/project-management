@extends('layouts.app')

@section('title', 'Edit Team Member - Digital Code')

@section('content')

    <div class="app-page max-w-3xl">

        <div class="mb-8">
            <a href="{{ route('team') }}" class="text-[13px] font-medium text-[#8A8C90] hover:text-[#B8912B]">← Kembali ke
                Team</a>
            <h1 class="mt-2 font-['Newsreader'] text-[30px] font-medium text-[#10131A]">Edit Team Member</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 border-l-2 border-red-500 bg-red-500/5 px-4 py-3.5 text-[14px] text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="max-w-xl rounded-2xl border border-[#E6E2D6] bg-white p-7">
            <form action="{{ route('team.update', $member) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Nama <span
                            class="text-red-500">*</span></label>
                            <input type="text" value="{{ $member->user->name }}" disabled
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Email</label>
                            <input type="email" value="{{ $member->user->email }}" disabled
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role" required
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            @foreach (['admin' => 'Admin', 'member' => 'Member'] as $value => $role)
                                <option value="{{ $value }}" @selected(old('role', $member->role) === $value)>{{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Posisi</label>
                        <input type="text" name="position" value="{{ old('position', $member->position) }}"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8912B] px-6 py-2.5 text-[14px] font-semibold text-[#14171F] hover:brightness-105">
                        Update Member
                    </button>
                    <a href="{{ route('team') }}"
                        class="rounded-lg px-6 py-2.5 text-[14px] font-medium text-[#6B6D72] hover:bg-[#F4F3EF]">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection
