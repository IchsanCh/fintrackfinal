<x-layouts.core title="Admin Dashboard — FinTrack">

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="text-xs font-mono uppercase tracking-[0.2em] text-error font-semibold mb-1">
            Administrator
        </p>
        <h1 class="text-2xl font-semibold tracking-tight text-base-content">
            Halo, {{ Str::words($user->name, 1, '') }} 👋
        </h1>
        <p class="text-sm text-base-content/50 mt-1">Ringkasan keseluruhan platform FinTrack.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        {{-- Total User --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Total User
                </span>
                <div class="w-8 h-8 rounded-lg bg-primary/15 grid place-items-center">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                    {{ number_format($stats['total_users']) }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">
                    +{{ $stats['new_users_this_month'] }} bulan ini
                </p>
            </div>
        </div>

        {{-- User Aktif --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    User Aktif
                </span>
                <div class="w-8 h-8 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-success">
                    {{ number_format($stats['active_users']) }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">status aktif</p>
            </div>
        </div>

        {{-- User Banned --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Dibanned
                </span>
                <div class="w-8 h-8 rounded-lg bg-error/15 grid place-items-center">
                    <svg class="w-4 h-4 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-error">
                    {{ number_format($stats['banned_users']) }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">akun dinonaktifkan</p>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Transaksi
                </span>
                <div class="w-8 h-8 rounded-lg bg-secondary/15 grid place-items-center">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                    {{ number_format($stats['total_transactions']) }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">total platform</p>
            </div>
        </div>

    </div>

    {{-- User terbaru --}}
    <div class="rounded-xl bg-base-200 border border-base-300 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-base-300">
            <h2 class="text-sm font-semibold text-base-content">User Terbaru</h2>
            <a href="{{ route('admin.users.index') }}"
                class="text-xs text-primary hover:text-primary/70 font-mono transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($recentUsers->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <p class="text-sm text-base-content/40">Belum ada pengguna</p>
            </div>
        @else
            <ul class="divide-y divide-base-300">
                @foreach ($recentUsers as $u)
                    <li class="flex items-center gap-4 px-5 py-3.5 hover:bg-base-300/30 transition-colors">
                        <div class="avatar avatar-placeholder shrink-0">
                            <div class="bg-primary/20 rounded-full w-9 grid place-items-center">
                                <span class="text-xs font-bold font-mono text-primary">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-base-content truncate">{{ $u->name }}</p>
                            <p class="text-xs text-base-content/40 mt-0.5 truncate font-mono">{{ $u->email }}</p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span
                                class="badge badge-sm font-mono
                                {{ $u->status === 'active' ? 'badge-success' : 'badge-error' }}
                                badge-soft">
                                {{ $u->status }}
                            </span>
                            <span class="text-xs text-base-content/35 font-mono">
                                {{ $u->created_at->locale('id')->diffForHumans() }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</x-layouts.core>
