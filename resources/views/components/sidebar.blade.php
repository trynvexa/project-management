<aside class="fixed inset-y-0 left-0 z-40 hidden w-64 border-r border-slate-200 bg-white lg:block">
    <div class="flex h-full flex-col">

        <!-- Logo -->
        <div class="flex h-20 items-center border-b border-slate-200 px-6">
            <div>
                <h1 class="text-xl font-bold text-slate-900">
                    DIGITAL CODE
                </h1>
                <p class="text-xs text-slate-500">
                    Digital Code
                </p>
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 space-y-1 px-4 py-6">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-lg bg-slate-900 px-4 py-3 text-sm font-medium text-white">
                <span>▦</span>
                Dashboard
            </a>

            <a href="#"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100">
                <span>▣</span>
                Projects
            </a>

            <a href="#"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100">
                <span>✓</span>
                Tasks
            </a>

            <a href="#"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100">
                <span>♙</span>
                Clients
            </a>

            <a href="#"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100">
                <span>♧</span>
                Team
            </a>

        </nav>

        <!-- Logout -->
        <div class="border-t border-slate-200 p-4">
            <button
                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50">
                <span>↪</span>
                Logout
            </button>
        </div>

    </div>
</aside>
