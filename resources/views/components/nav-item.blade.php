@props(['route' => null, 'label', 'icon', 'active' => false, 'disabled' => false])
@if ($disabled)
    <span class="nav-item nav-disabled" title="Coming soon"><x-icon :name="$icon" class="h-[18px] w-[18px]" /><span
            class="brand-copy flex-1">{{ $label }}</span><small
            class="brand-copy rounded-md bg-white/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide">Soon</small></span>
@else<a href="{{ route($route) }}" class="nav-item {{ $active ? 'nav-active' : '' }}"><x-icon :name="$icon"
            class="h-[18px] w-[18px]" /><span class="brand-copy">{{ $label }}</span></a>
@endif
