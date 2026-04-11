@php
    $menu = [
        [
            'label' => 'Utama',
            'items' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon' =>
                        '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                ],
                [
                    'name' => 'Transaksi',
                    'route' => 'transactions.index',
                    'icon' =>
                        '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
                ],
                [
                    'name' => 'Akun',
                    'route' => 'accounts.index',
                    'icon' =>
                        '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
                ],
            ],
        ],
        [
            'label' => 'Perencanaan',
            'items' => [
                [
                    'name' => 'Budget',
                    'route' => 'budgets.index',
                    'icon' =>
                        '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
                ],
                [
                    'name' => 'Tabungan',
                    'route' => 'saving-goals.index',
                    'icon' =>
                        '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                ],
                [
                    'name' => 'Tagihan',
                    'route' => 'bill-reminders.index',
                    'icon' =>
                        '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
                ],
            ],
        ],
        [
            'label' => 'Lainnya',
            'items' => [
                [
                    'name' => 'Kategori',
                    'route' => 'categories.index',
                    'icon' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
                ],
                [
                    'name' => 'Notifikasi',
                    'route' => 'notifications.index',
                    'icon' =>
                        '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
                ],
            ],
        ],
    ];
@endphp

<aside class="w-64 min-h-screen bg-base-200 border-r border-base-300 flex flex-col">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-base-300 shrink-0">
        <div class="w-7 h-7 rounded-lg bg-primary/20 grid place-items-center">
            <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23" />
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
        </div>
        <span class="font-mono font-bold text-sm tracking-widest text-base-content uppercase">
            FinTrack
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
        @foreach ($menu as $group)
            <div>
                <p
                    class="px-3 mb-1.5 text-[10px] font-mono font-semibold uppercase tracking-[0.18em] text-base-content/35">
                    {{ $group['label'] }}
                </p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $active = request()->routeIs($item['route']);
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                                      {{ $active ? 'bg-primary/15 text-primary' : 'text-base-content/60 hover:bg-base-300/60 hover:text-base-content' }}">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $item['icon'] !!}
                                </svg>
                                {{ $item['name'] }}

                                @if ($active)
                                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Footer sidebar: info user --}}
    <div class="px-4 py-4 border-t border-base-300 shrink-0">
        <div class="flex items-center gap-3">
            <div class="avatar avatar-placeholder">
                <div class="bg-primary/20 rounded-full w-8 grid place-items-center">
                    <span class="text-xs font-bold font-mono text-primary">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()?->name }}</p>
                <p class="text-[11px] text-base-content/40 truncate font-mono">{{ auth()->user()?->email }}</p>
            </div>
        </div>
    </div>

</aside>
