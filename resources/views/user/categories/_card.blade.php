<div
    class="rounded-xl bg-white/5 border border-base-300 p-4 flex items-center gap-4
            hover:border-{{ $color }}/30 transition-colors group">

    {{-- Icon --}}
    <div class="w-10 h-10 rounded-lg bg-{{ $color }}/10 grid place-items-center shrink-0">
        <x-ui.icon :name="$cat->icon ?? 'ellipsis-horizontal'" class="w-5 h-5 text-{{ $color }}" />
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-base-content truncate">{{ $cat->name }}</p>
        <span class="text-[11px] font-mono text-base-content/50">
            {{ $cat->transactions()->count() }} transaksi
        </span>
    </div>

    {{-- Actions --}}
    <div class="dropdown dropdown-end">
        <div tabindex="0" role="button"
            class="btn btn-ghost btn-xs btn-square text-base-content/30 hover:text-base-content">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="5" r="1" />
                <circle cx="12" cy="12" r="1" />
                <circle cx="12" cy="19" r="1" />
            </svg>
        </div>
        <ul tabindex="0"
            class="dropdown-content menu menu-sm z-50 mt-1 w-36 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
            <li>
                <button
                    onclick="openEditCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->icon }}', '{{ $cat->type }}')"
                    class="rounded-lg text-sm">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit
                </button>
            </li>
            <li>
                <button onclick="openDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                    class="rounded-lg text-sm text-error">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                    Hapus
                </button>
            </li>
        </ul>
    </div>
</div>
