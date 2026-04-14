@php
    $menu = [
        [
            'label' => 'Overview',
            'items' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' =>
                        '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                ],
            ],
        ],
        [
            'label' => 'Manajemen',
            'items' => [
                [
                    'name' => 'Pengguna',
                    'route' => 'admin.users.index',
                    'icon' =>
                        '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                ],
                [
                    'name' => 'Pengumuman',
                    'route' => 'admin.announcements.index',
                    'icon' =>
                        '<path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"/>',
                ],
            ],
        ],
    ];
@endphp

<aside class="w-64 min-h-screen bg-base-200 border-r border-base-300 flex flex-col">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-base-300 shrink-0">
        <div class="w-7 h-7 rounded-lg bg-error/20 grid place-items-center">
            <svg class="w-4 h-4 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
        </div>
        <div>
            <span class="font-mono font-bold text-sm tracking-widest text-base-content uppercase block">
                FinTrack
            </span>
            <span class="font-mono text-[10px] tracking-widest text-error/70 uppercase">
                Admin
            </span>
        </div>
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
                            {{-- {{ route($item['route']) }} --}}
                            <a href=""
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                                      {{ $active ? 'bg-error/10 text-error' : 'text-base-content/60 hover:bg-base-300/60 hover:text-base-content' }}">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $item['icon'] !!}
                                </svg>
                                {{ $item['name'] }}

                                @if ($active)
                                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-error"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Footer sidebar: info admin --}}
    <div class="px-4 py-4 border-t border-base-300 shrink-0">
        <div class="flex items-center gap-3">
            <div class="avatar avatar-placeholder">
                <div class="bg-error/20 rounded-full w-8 grid place-items-center">
                    <span class="text-xs font-bold font-mono text-error">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()?->name }}</p>
                <p class="text-[11px] text-error/50 truncate font-mono">Administrator</p>
            </div>
        </div>
    </div>

</aside>
