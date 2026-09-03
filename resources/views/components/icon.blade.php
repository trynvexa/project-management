@props(['name', 'class' => 'h-5 w-5'])
<svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" stroke-width="1.8"
    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
    @switch($name)
        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
        @break

        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
        @break

        @case('folder')
            <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
        @break

        @case('check-square')
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <path d="m8 12 2.5 2.5L16 9" />
        @break

        @case('kanban')
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <path d="M8 8v8M16 8v5" />
        @break

        @case('team')
            <circle cx="9" cy="7" r="3" />
            <path d="M3 21v-2a6 6 0 0 1 12 0v2M16 11a3 3 0 1 0-1.5-5.6M19 21v-2a6 6 0 0 0-3-5.2" />
        @break

        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18" />
        @break

        @case('chart')
            <path d="M4 19V5M4 19h16M8 16v-4M12 16V8M16 16V5" />
        @break

        @case('pulse')
            <path d="M3 12h4l2-7 4 14 2-7h6" />
        @break

        @case('bell')
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
        @break

        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path
                d="M19 15a2 2 0 0 0 .4 2.2l-2.1 2.1a2 2 0 0 0-2.2.4 2 2 0 0 0-1.1 1.8h-3a2 2 0 0 0-1.1-1.8 2 2 0 0 0-2.2-.4l-2.1-2.1A2 2 0 0 0 6 15a2 2 0 0 0-1.8-1.1v-3A2 2 0 0 0 6 9.8a2 2 0 0 0-.4-2.2l2.1-2.1a2 2 0 0 0 2.2-.4A2 2 0 0 0 11 3.4h3A2 2 0 0 0 15.1 5a2 2 0 0 0 2.2.4l2.1 2.1A2 2 0 0 0 19 9.8a2 2 0 0 0 1.8 1.1v3A2 2 0 0 0 19 15z" />
        @break

        @case('help')
            <circle cx="12" cy="12" r="9" />
            <path d="M9.5 9a2.5 2.5 0 1 1 4.4 1.6c-1.4 1.4-1.9 1.7-1.9 3.4M12 17h.01" />
        @break

        @case('logout')
            <path d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 0 0-2-2h-6" />
        @break

        @case('panel-left')
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <path d="M9 3v18M13 9l3 3-3 3" />
        @break

        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
        @break

        @case('search')
            <circle cx="11" cy="11" r="6" />
            <path d="m20 20-4-4" />
        @break

        @case('more')
            <circle cx="5" cy="12" r="1" fill="currentColor" />
            <circle cx="12" cy="12" r="1" fill="currentColor" />
            <circle cx="19" cy="12" r="1" fill="currentColor" />
        @break

        @case('trash')
            <path d="M3 6h18M8 6V4h8v2m2 0-1 15H7L6 6M10 11v5m4-5v5" />
        @break
    @endswitch
</svg>
