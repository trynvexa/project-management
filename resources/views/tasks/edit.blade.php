@extends('layouts.app')

@section('title', 'Edit Task - Digital Code')
@section('page_title', 'Edit task')

@section('content')

    <div class="app-page max-w-4xl">

        <div class="mb-8">
            <a href="{{ route('tasks') }}" class="text-[13px] font-medium text-[#8A8C90] hover:text-[#B8912B]">← Kembali ke
                Tasks</a>
            <h1 class="mt-2 font-['Newsreader'] text-[30px] font-medium text-[#10131A]">Edit Task</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 border-l-2 border-red-500 bg-red-500/5 px-4 py-3.5 text-[14px] text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="max-w-2xl rounded-2xl border border-[#E6E2D6] bg-white p-7">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Nama Task <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $task->name) }}" required
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Project</label>
                        <select name="project_id"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            <option value="">Pilih project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id', $task->project_id) == $project->id)>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Member</label>
                        <select name="assignee_id"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            <option value="">Pilih member</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('assignee_id', $task->assignee_id) == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $task->deadline) }}"
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Priority <span
                                class="text-red-500">*</span></label>
                        <select name="priority" required
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            @foreach (['High', 'Medium', 'Low'] as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', $task->priority) === $priority)>{{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">
                            @foreach (['Todo', 'In Progress', 'Review', 'Done'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $task->status) === $status)>{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[13px] font-medium text-[#6B6D72]">Deskripsi</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-lg border border-[#E6E2D6] px-4 py-2.5 text-[14px] focus:border-[#B8912B] focus:outline-none">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8912B] px-6 py-2.5 text-[14px] font-semibold text-[#14171F] hover:brightness-105">
                        Update Task
                    </button>
                    <a href="{{ route('tasks') }}"
                        class="rounded-lg px-6 py-2.5 text-[14px] font-medium text-[#6B6D72] hover:bg-[#F4F3EF]">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection
