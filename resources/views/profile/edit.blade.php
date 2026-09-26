@extends('layouts.app')
@section('title', 'Settings · Digital Code') @section('page_title', 'Account settings') @section('breadcrumb', 'Settings')
@section('content')
    <div class="app-page max-w-6xl">
        <section class="panel relative overflow-hidden bg-[var(--nb-blue)]">
            <div class="absolute -right-5 -top-8 text-[9rem] font-black leading-none opacity-15">@</div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="relative"><img id="headerAvatar"
                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=ff8bb3&color=161616' }}"
                        class="h-24 w-24 rounded-xl border-[3px] border-[var(--nb-line)] object-cover shadow-[5px_5px_0_var(--nb-line)]"
                        alt="{{ $user->name }}"><span
                        class="absolute -bottom-2 -right-2 rounded-md border-2 border-[var(--nb-line)] bg-[var(--nb-green)] px-2 py-1 text-[10px] font-extrabold">{{ ucfirst($user->role ?? 'member') }}</span>
                </div>
                <div>
                    <p class="text-sm font-bold">Your account</p>
                    <h2 class="mt-1 font-display text-2xl font-extrabold">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm">{{ $user->email }}</p>
                </div>
            </div>
        </section>
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="panel">
                <div class="section-head">
                    <div>
                        <h2>Profile editor</h2>
                        <p>Changes are saved to your account.</p>
                    </div>
                </div>
                @if ($errors->has('name') || $errors->has('email') || $errors->has('avatar'))
                    <p class="mt-4 rounded-lg border-2 border-[var(--nb-line)] bg-[var(--nb-pink)] p-3 text-sm font-bold">
                        {{ $errors->first('name') ?: ($errors->first('email') ?: $errors->first('avatar')) }}</p>
                @endif
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                    class="mt-6 space-y-5" x-data="{ preview: null }">
                    @csrf @method('PUT')<div><label class="form-label">Avatar</label>
                        <div class="flex items-center gap-4"><img
                                :src="preview ||
                                    '{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=ff8bb3&color=161616' }}'"
                                class="h-16 w-16 rounded-lg border-2 border-[var(--nb-line)] object-cover"
                                alt="Avatar preview"><label class="btn-secondary cursor-pointer">Choose image<input
                                    type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden"
                                    @change="preview=URL.createObjectURL($event.target.files[0])"></label></div>
                        <p class="mt-2 text-xs text-slate-500">JPG, PNG, or WEBP · maximum 10 MB.</p>
                    </div>
                    <div><label class="form-label">Name</label><input class="form-input" name="name"
                            value="{{ old('name', $user->name) }}" required></div>
                    <div><label class="form-label">Email</label><input class="form-input" type="email" name="email"
                            value="{{ old('email', $user->email) }}" required></div><button
                        class="btn-primary w-full sm:w-auto" type="submit" data-loading="Saving...">SAVE CHANGES</button>
                </form>
            </section>
            <section class="panel">
                <div class="section-head">
                    <div>
                        <h2>Security</h2>
                        <p>Use a unique, strong password.</p>
                    </div>
                </div>
                @if ($errors->has('current_password') || $errors->has('password'))
                    <p class="mt-4 rounded-lg border-2 border-[var(--nb-line)] bg-[var(--nb-pink)] p-3 text-sm font-bold">
                        {{ $errors->first('current_password') ?: $errors->first('password') }}</p>
                @endif
                <form action="{{ route('profile.password') }}" method="POST" class="mt-6 space-y-5">@csrf
                    @method('PUT')<div><label class="form-label">Current password</label><input class="form-input"
                            type="password" name="current_password" required></div>
                    <div><label class="form-label">New password</label><input class="form-input" type="password"
                            name="password" required></div>
                    <div><label class="form-label">Confirm new password</label><input class="form-input" type="password"
                            name="password_confirmation" required></div><button class="btn-secondary w-full sm:w-auto"
                        type="submit" data-loading="Updating...">UPDATE PASSWORD</button>
                </form>
            </section>
        </div>
    </div>
@endsection
