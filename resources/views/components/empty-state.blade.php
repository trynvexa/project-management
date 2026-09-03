<div class="py-12 text-center">
    <div class="mx-auto grid h-11 w-11 place-items-center rounded-2xl bg-indigo-50 text-lg font-bold text-indigo-600">+
    </div>
    <h3 class="mt-4 text-sm font-semibold text-slate-800">Nothing here yet</h3>
    <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">{{ $message }}</p>
    @isset($href)
        <a href="{{ $href }}" class="btn-primary mt-5">{{ $action ?? 'Get started' }}</a>
    @endisset
</div>
