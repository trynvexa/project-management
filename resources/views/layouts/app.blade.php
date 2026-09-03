<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Flowbase')</title>
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#ffd84d">
    <link rel="apple-touch-icon" href="{{ asset('images/pwa-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<script>
    document.documentElement.dataset.theme = localStorage.getItem('flowbase-theme') || ((matchMedia(
        '(prefers-color-scheme: dark)').matches) ? 'dark' : 'light')
</script>

<body>
    <div id="appShell" class="min-h-screen" x-data>
        <aside id="sidebar"
            class="sidebar fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col lg:translate-x-0">
            <div class="flex h-[76px] items-center border-b border-white/10 px-5">
                <a href="{{ route('dashboard') }}" class="brand flex min-w-0 items-center gap-3">
                    <span
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-lg font-extrabold text-white shadow-lg shadow-indigo-950/30">F</span>
                    <span class="brand-copy min-w-0">
                        <b class="block font-display text-[15px] tracking-tight text-white">Flowbase</b>
                        <small class="block text-[11px] text-slate-400">Project workspace</small>
                    </span>
                </a>
                <button id="collapseSidebar"
                    class="ml-auto hidden rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white lg:block"
                    aria-label="Collapse navigation">
                    <x-icon name="panel-left" class="h-4 w-4" />
                </button>
            </div>
            <nav class="sidebar-nav flex-1 overflow-y-auto px-3 py-5" aria-label="Main navigation">
                <p class="nav-label">Main</p>
                <x-nav-item route="dashboard" label="Dashboard" icon="grid" :active="request()->routeIs('dashboard')" />
                <p class="nav-label mt-6">Management</p>
                <x-nav-item route="clients" label="Clients" icon="users" :active="request()->routeIs('clients*')" />
                <x-nav-item route="projects" label="Projects" icon="folder" :active="request()->routeIs('projects*')" />
                <x-nav-item route="tasks" label="Tasks" icon="check-square" :active="request()->routeIs('tasks') ||
                    request()->routeIs('tasks.create') ||
                    request()->routeIs('tasks.show') ||
                    request()->routeIs('tasks.edit')" />
                <x-nav-item route="tasks.board" label="Kanban" icon="kanban" :active="request()->routeIs('tasks.board')" />
                <p class="nav-label mt-6">Workspace</p>
                <x-nav-item route="team" label="Team" icon="team" :active="request()->routeIs('team*')" />
                <x-nav-item route="calendar" label="Calendar" icon="calendar" :active="request()->routeIs('calendar')" />
                <p class="nav-label mt-6">Analytics</p>
                <x-nav-item route="reports" label="Reports" icon="chart" :active="request()->routeIs('reports')" />
                <x-nav-item route="activity" label="Activity" icon="pulse" :active="request()->routeIs('activity')" />
                <p class="nav-label mt-6">System</p>
                <x-nav-item route="search" label="Search" icon="search" :active="request()->routeIs('search')" />
                <x-nav-item route="notifications.index" label="Notifications" icon="bell" :active="request()->routeIs('notifications.*')" />
                <x-nav-item route="profile.edit" label="Settings" icon="settings" :active="request()->routeIs('profile.*')" />
            </nav>
            <div class="border-t border-white/10 p-3">
                <a href="{{ route('profile.edit') }}" class="user-card group flex items-center gap-3 rounded-xl p-2.5">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            class="h-9 w-9 rounded-full border-2 border-[var(--nb-line)] object-cover"
                        alt="">@else<span
                            class="avatar h-9 w-9 bg-indigo-500/20 text-xs text-indigo-200">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    @endif
                    <span class="brand-copy min-w-0 flex-1">
                        <b class="block truncate text-sm text-white">{{ auth()->user()->name }}</b>
                        <small class="block truncate text-xs text-slate-400">{{ auth()->user()->email }}</small>
                    </span>
                    <x-icon name="more" class="brand-copy h-4 w-4 text-slate-500" />
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">@csrf<button
                        class="nav-item w-full text-left text-slate-400 hover:text-rose-300">
                        <x-icon name="logout" class="h-[18px] w-[18px]" />
                        <span class="brand-copy">Sign out</span>
                    </button>
                </form>
            </div>
        </aside>
        <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden">
        </div>
        <div id="contentShell" class="min-h-screen transition-[padding] duration-300 lg:pl-[272px]">
            <header class="topbar sticky top-0 z-30 flex h-[76px] items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button id="menuButton" aria-label="Open navigation" aria-expanded="false" aria-controls="sidebar" class="icon-button lg:hidden">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>
                <div class="min-w-0">
                    <div class="hidden items-center gap-1 text-xs text-slate-400 sm:flex">
                        <span>Workspace</span>
                        <span>/</span>
                        <span>@yield('breadcrumb', 'Overview')</span>
                    </div>
                    <h1 class="truncate font-display text-base font-bold text-slate-900">@yield('page_title', 'Overview')</h1>
                </div>
                @if ($workspaceOptions->count() > 1)
                    <form method="POST" action="{{ route('workspaces.switch', session('current_workspace_id')) }}"
                        class="hidden lg:block">@csrf<select name="workspace_id"
                            onchange="this.form.action='{{ url('/workspaces') }}/'+this.value+'/switch';this.form.submit()"
                            class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs"
                            aria-label="Switch workspace">
                            @foreach ($workspaceOptions as $workspace)
                                <option value="{{ $workspace->id }}" @selected($workspace->id === (int) session('current_workspace_id'))>
                                    {{ $workspace->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <div class="ml-auto flex items-center gap-2">
                    <form action="{{ route('search') }}" class="top-search hidden items-center lg:flex">
                        <x-icon name="search" class="h-4 w-4" />
                        <input name="q" value="{{ request('q') }}" aria-label="Search workspace"
                            placeholder="Search workspace..." />
                    </form>
                    <button x-data="themeToggle" @click="toggle" class="theme-toggle" type="button"
                        :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                        :title="dark ? 'Light mode' : 'Dark mode'">
                        <span x-text="dark ? '☀' : '☾'">
                        </span>
                        <b class="hidden sm:inline" x-text="dark ? 'Light' : 'Dark'">
                        </b>
                    </button>
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open=false">
                        <button @click="open=!open" class="icon-button relative" type="button"
                            aria-label="Open notifications" :aria-expanded="open">
                            <x-icon name="bell" class="h-5 w-5" />
                            @if ($unreadNotifications)
                                <span
                                    class="notification-count">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
                            @endif
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.right @click.outside="open=false" class="notification-popover" role="region" aria-label="Notifications">
                            <div class="flex items-center justify-between border-b-2 border-[var(--nb-line)] pb-3">
                                <b>Notifications</b>
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold underline">View
                                    all</a>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse($notificationPreview as $notification)
                                    <a href="{{ $notification->data['url'] ?? route('notifications.index') }}"
                                        class="block border-b border-[var(--nb-line)] p-3 hover:bg-[var(--nb-yellow)]">
                                        <b
                                            class="block text-sm">{{ $notification->data['title'] ?? 'Workspace update' }}</b>
                                        <small
                                            class="mt-1 block text-xs">{{ $notification->data['message'] ?? '' }}</small>
                                        <small
                                            class="mt-1 block text-[10px] opacity-70">{{ $notification->created_at->diffForHumans() }}</small>
                                </a>@empty<p class="p-6 text-center text-sm font-bold">You are all caught up.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="avatar h-9 w-9 overflow-hidden text-xs"
                        aria-label="Open profile">
                        @if (auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                class="h-full w-full object-cover"
                                alt="">@else{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        @endif
                    </a>
                </div>
            </header>
            <main class="page-enter">@yield('content')</main>
            <footer class="pb-24 px-6 py-7 text-center text-xs text-slate-400 lg:pb-7">© {{ date('Y') }} Flowbase
                · Made for focused teams.</footer>
        </div>
    </div>
    <nav class="mobile-nav lg:hidden" aria-label="Mobile navigation">
        <a href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">⌂<small>Home</small>
        </a>
        <a href="{{ route('projects') }}"
            class="{{ request()->routeIs('projects*') ? 'active' : '' }}">▣<small>Projects</small>
        </a>
        <a href="{{ route('tasks') }}" class="{{ request()->routeIs('tasks') ? 'active' : '' }}">✓<small>Tasks</small>
        </a>
        <a href="{{ route('tasks.board') }}"
            class="{{ request()->routeIs('tasks.board') ? 'active' : '' }}">▤<small>Kanban</small>
        </a>
        <button id="menuButtonBottom" type="button" aria-label="Open more navigation" aria-expanded="false" aria-controls="sidebar">☰<small>More</small>
        </button>
    </nav>
    <div id="toastRegion"
        class="pointer-events-none fixed right-4 top-4 z-[90] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3"
        aria-live="polite">
    </div>
    @if (session('success'))
        <div data-flash data-type="success" data-message="{{ session('success') }}">
        </div>
        @endif @if (session('error'))
            <div data-flash data-type="error" data-message="{{ session('error') }}">
            </div>
        @endif
        <div id="confirmModal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4" role="dialog"
            aria-modal="true" aria-labelledby="confirmTitle">
            <div data-modal-backdrop class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
            </div>
            <div data-modal-panel class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-rose-50 text-rose-600">
                    <x-icon name="trash" class="h-5 w-5" />
                </div>
                <h2 id="confirmTitle" class="mt-4 font-display text-xl font-bold text-slate-900">Delete this item?
                </h2>
                <p id="confirmMessage" class="mt-2 text-sm leading-6 text-slate-500">
                </p>
                <div class="mt-7 flex justify-end gap-3">
                    <button id="cancelConfirm" type="button" class="btn-secondary">Cancel</button>
                    <button id="confirmSubmit" type="button" class="btn-danger">Delete</button>
                </div>
            </div>
        </div>
</body>

</html>
