<header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
    <div class="flex h-20 items-center justify-between px-4 sm:px-6 lg:px-8">

        <div>
            <h2 class="text-lg font-semibold text-slate-900">
                @yield('page-title', 'Dashboard')
            </h2>

            <p class="hidden text-sm text-slate-500 sm:block">
                Manage your agency projects efficiently.
            </p>
        </div>

        <div class="flex items-center gap-3">

            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold text-slate-900">
                    Admin
                </p>

                <p class="text-xs text-slate-500">
                    Administrator
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                AD
            </div>

        </div>

    </div>
</header>
