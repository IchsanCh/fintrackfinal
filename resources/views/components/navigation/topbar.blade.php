@props(['title' => 'FinTrack'])

<header
    class="sticky top-0 z-30 flex items-center justify-between
               h-16 px-4 lg:px-8
               bg-base-100/80 backdrop-blur-md
               border-b border-base-300">

    {{-- Kiri: hamburger (mobile) + page title --}}
    <div class="flex items-center gap-3">
        {{-- Hamburger toggle (hanya muncul di mobile) --}}
        <label for="main-drawer"
            class="btn btn-ghost btn-sm btn-square lg:hidden text-base-content/60 hover:text-base-content">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </label>

        <h1 class="text-sm font-semibold text-base-content/70 font-mono tracking-wide">
            {{ $title }}
        </h1>
    </div>

    {{-- Kanan: notifikasi + avatar dropdown --}}
    <div class="flex items-center gap-2">

        {{-- Notifikasi (hanya user) --}}
        @if (auth()->user()?->role === 'user')
            @php
                $totalUnread =
                    auth()->user()->notifications()->where('is_read', false)->count() +
                    \App\Models\Announcement::where('is_active', true)
                        ->whereNotIn(
                            'id',
                            \App\Models\AnnouncementRead::where('user_id', auth()->id())->pluck('announcement_id'),
                        )
                        ->count();
            @endphp
            <a href="{{ route('notifications.index') }}"
                class="btn btn-ghost btn-sm btn-square relative text-base-content/60 hover:text-base-content">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                @if ($totalUnread > 0)
                    <span
                        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center
                             bg-error text-[10px] font-bold font-mono text-white rounded-full px-1">
                        {{ $totalUnread > 99 ? '99+' : $totalUnread }}
                    </span>
                @endif
            </a>
        @endif

        {{-- Avatar dropdown --}}
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-sm gap-2 px-2 hover:bg-base-200 rounded-lg">
                <div class="avatar avatar-placeholder">
                    <div class="bg-primary/20 text-primary-content rounded-full w-8 grid place-items-center">
                        <span class="text-xs font-bold font-mono">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                </div>
                <span class="hidden sm:block text-sm font-medium max-w-[120px] truncate">
                    {{ auth()->user()?->name }}
                </span>
                <svg class="w-3.5 h-3.5 text-base-content/40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>

            <ul tabindex="0"
                class="dropdown-content menu menu-sm z-50 mt-2 w-52 rounded-xl
                       bg-base-200 border border-base-300 shadow-xl p-2 gap-0.5">
                <li class="menu-title px-3 py-1">
                    <span class="text-[11px] font-mono uppercase tracking-widest text-base-content/40">
                        Akun
                    </span>
                </li>
                <li>
                    <a href="#" class="rounded-lg text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Profil
                    </a>
                </li>
                <li>
                    <a href="#" class="rounded-lg text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06
                                     a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09
                                     A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83
                                     l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                                     A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83
                                     l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09
                                     a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83
                                     l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09
                                     a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        Pengaturan
                    </a>
                </li>
                <li class="mt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-lg text-sm text-error hover:bg-error/10 flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>
