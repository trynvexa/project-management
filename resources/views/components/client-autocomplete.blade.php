@props(['clients', 'selectedId' => null, 'name' => 'client_id'])
@php($selected = $clients->firstWhere('id', (int) $selectedId))
<div x-data="clientAutocomplete({{ Illuminate\Support\Js::from($clients->map(fn($client) => ['id' => $client->id, 'name' => $client->name, 'email' => $client->email])->values()) }}, {{ Illuminate\Support\Js::from($selected ? ['id' => $selected->id, 'name' => $selected->name, 'email' => $selected->email] : null) }})" class="relative" @keydown.escape="open=false">
    <label class="form-label" for="client-search">Client</label>
    <input type="hidden" name="{{ $name }}" :value="selected?.id || ''">
    <div class="relative"><input id="client-search" x-ref="input" class="form-input pr-10" type="text" role="combobox"
            autocomplete="off" :aria-expanded="open" aria-controls="client-results"
            :aria-activedescendant="open && results[active] ? 'client-option-' + results[active].id : ''"
            placeholder="Search a client..." x-model="query" @focus="open=true" @input="open=true; active=0"
            @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)"
            @keydown.enter.prevent="choose(results[active])" @keydown.tab="open=false">
        <button x-show="selected || query" x-cloak type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-lg font-bold leading-none hover:bg-[var(--nb-pink)]"
            @click="clear" aria-label="Clear selected client">×</button>
    </div>
    <p x-show="selected" x-cloak
        class="mt-2 inline-flex items-center gap-2 rounded-md border-2 border-[var(--nb-line)] bg-[var(--nb-green)] px-2 py-1 text-xs font-bold">
        <span x-text="selected.name"></span><span x-show="selected.email" class="font-medium"
            x-text="selected.email"></span>
    </p>
    <div id="client-results" x-show="open" x-cloak x-transition.origin.top
        class="absolute z-30 mt-2 max-h-64 w-full overflow-y-auto rounded-lg border-2 border-[var(--nb-line)] bg-[var(--nb-surface)] p-1 shadow-[5px_5px_0_var(--nb-line)]"
        role="listbox">
        <template x-for="(client,index) in results" :key="client.id"><button type="button"
                :id="'client-option-' + client.id" role="option" :aria-selected="selected?.id === client.id"
                @mouseenter="active=index" @click="choose(client)"
                class="block w-full rounded-md px-3 py-3 text-left transition"
                :class="active === index ? 'bg-[var(--nb-yellow)]' : 'hover:bg-[var(--nb-yellow)]'"><b
                    class="block text-sm" x-text="client.name"></b><small class="block text-xs opacity-70"
                    x-text="client.email || 'No email address'"></small></button></template>
        <p x-show="!results.length" class="p-4 text-center text-sm font-bold">No matching clients.</p>
    </div>
</div>
