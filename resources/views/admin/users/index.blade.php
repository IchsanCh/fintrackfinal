<x-layouts.core title="Manajemen User">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-base-content">Manajemen User</h1>
        <p class="text-sm text-base-content/50 mt-1">Kelola semua pengguna platform FinTrack.</p>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-6"><x-ui.alert type="success">{{ session('success') }}</x-ui.alert></div>
    @endif
    @if ($errors->any())
        <div class="mb-6"><x-ui.alert type="error">
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
            </x-ui.alert></div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-base-content">{{ $totalUsers }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Total User</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-success">{{ $activeUsers }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Aktif</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-error">{{ $bannedUsers }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Banned</p>
        </div>
    </div>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3 mb-6">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="w-4 h-4 z-10 text-base-content/30 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                class="input input-primary !py-2 !pl-10 w-full" />
        </div>
        <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-base-300">
            @foreach (['' => 'Semua', 'active' => 'Aktif', 'banned' => 'Banned'] as $val => $label)
                <button type="submit" name="status" value="{{ $val }}"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                               {{ ($status ?? '') === $val
                                   ? ($val === 'active'
                                       ? 'bg-success/15 text-success'
                                       : ($val === 'banned'
                                           ? 'bg-error/15 text-error'
                                           : 'bg-primary/15 text-primary'))
                                   : 'text-base-content/50 hover:text-base-content' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- User table --}}
    <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
        @if ($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-sm text-base-content/40">Tidak ada user ditemukan</p>
            </div>
        @else
            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-base-300">
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                User</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Tier</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Akun</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Transaksi</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Status</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Bergabung</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr class="border-b border-base-300/50 hover:bg-white/[0.02] transition-colors">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar avatar-placeholder shrink-0">
                                            <div class="bg-primary/20 rounded-full w-9 grid place-items-center">
                                                <span class="text-xs font-bold font-mono text-primary">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-base-content truncate">
                                                {{ $u->name }}</p>
                                            <p class="text-[11px] text-base-content/35 font-mono truncate">
                                                {{ $u->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php $plan = $u->activeSubscription?->plan; @endphp
                                    <span
                                        class="badge badge-sm badge-soft font-mono text-[10px]
                                        {{ $plan
                                            ? match ($plan->tier) {
                                                'sultan' => 'badge-warning',
                                                'premium' => 'badge-primary',
                                                default => 'badge-ghost',
                                            }
                                            : 'badge-ghost' }}">
                                        {{ $plan?->name ?? 'Free' }}
                                    </span>
                                </td>
                                <td class="text-sm font-mono text-base-content/50">{{ $u->accounts_count }}</td>
                                <td class="text-sm font-mono text-base-content/50">{{ $u->transactions_count }}</td>
                                <td>
                                    <span
                                        class="badge badge-sm font-mono {{ $u->status === 'active' ? 'badge-success' : 'badge-error' }} badge-soft">
                                        {{ $u->status }}
                                    </span>
                                </td>
                                <td class="text-xs text-base-content/35 font-mono">
                                    {{ $u->created_at->locale('id')->isoFormat('D MMM YY') }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-1 justify-end">
                                        <a href="{{ route('admin.users.show', $u) }}"
                                            class="btn btn-ghost btn-xs text-base-content/40 hover:text-primary">
                                            Detail
                                        </a>
                                        @if ($u->status === 'active')
                                            <form method="POST" action="{{ route('admin.users.ban', $u) }}"
                                                onsubmit="return confirm('Ban user {{ $u->name }}?')">
                                                @csrf @method('PATCH')
                                                <button
                                                    class="btn btn-ghost btn-xs text-error/50 hover:text-error">Ban</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.unban', $u) }}"
                                                onsubmit="return confirm('Aktifkan kembali {{ $u->name }}?')">
                                                @csrf @method('PATCH')
                                                <button
                                                    class="btn btn-ghost btn-xs text-success/50 hover:text-success">Unban</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <ul class="sm:hidden divide-y divide-base-300/50">
                @foreach ($users as $u)
                    <li class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="avatar avatar-placeholder shrink-0">
                                <div class="bg-primary/20 rounded-full w-9 grid place-items-center">
                                    <span class="text-xs font-bold font-mono text-primary">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-base-content truncate">{{ $u->name }}</p>
                                <p class="text-[11px] text-base-content/35 font-mono truncate">{{ $u->email }}</p>
                            </div>
                            <span
                                class="badge badge-sm font-mono {{ $u->status === 'active' ? 'badge-success' : 'badge-error' }} badge-soft">
                                {{ $u->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.users.show', $u) }}"
                                class="btn btn-ghost btn-xs text-primary">Detail</a>
                            @if ($u->status === 'active')
                                <form method="POST" action="{{ route('admin.users.ban', $u) }}"
                                    onsubmit="return confirm('Ban?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-ghost btn-xs text-error">Ban</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.unban', $u) }}"
                                    onsubmit="return confirm('Unban?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-ghost btn-xs text-success">Unban</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Pagination --}}
    @if ($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif

</x-layouts.core>
