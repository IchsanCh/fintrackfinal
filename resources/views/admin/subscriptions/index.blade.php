<x-layouts.core title="Subscriptions">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Subscriptions</h1>
            <p class="text-sm text-base-content/50 mt-1">Monitor dan kelola langganan pengguna.</p>
        </div>
        <button onclick="document.getElementById('modal-assign').showModal()"
            class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Assign Manual
        </button>
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

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-success">{{ $totalActive }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Aktif</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-warning">{{ $totalExpired }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Expired</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-error">{{ $totalCancelled }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Cancelled</p>
        </div>
    </div>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="flex flex-wrap items-end gap-3 mb-6">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="z-10 w-4 h-4 text-base-content/30 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari user..."
                class="input input-primary !py-2 !pl-10 w-full" />
        </div>
        <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-base-300">
            @foreach (['' => 'Semua', 'active' => 'Aktif', 'expired' => 'Expired', 'cancelled' => 'Cancelled'] as $val => $label)
                <button type="submit" name="status" value="{{ $val }}"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                               {{ ($status ?? '') === $val
                                   ? ($val === 'active'
                                       ? 'bg-success/15 text-success'
                                       : ($val === 'expired'
                                           ? 'bg-warning/15 text-warning'
                                           : ($val === 'cancelled'
                                               ? 'bg-error/15 text-error'
                                               : 'bg-primary/15 text-primary')))
                                   : 'text-base-content/50 hover:text-base-content' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- Table --}}
    <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
        @if ($subscriptions->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="text-sm text-base-content/40">Tidak ada subscription ditemukan</p>
            </div>
        @else
            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-base-300">
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                User</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Plan</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Status</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Mulai</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                                Expired</th>
                            <th
                                class="text-[10px] font-mono uppercase tracking-widest text-base-content/30 font-semibold">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $sub)
                            @php
                                $isReallyActive =
                                    $sub->status === 'active' &&
                                    (is_null($sub->expired_at) || $sub->expired_at->isFuture());
                                $isReallyExpired =
                                    $sub->status === 'active' && $sub->expired_at && $sub->expired_at->isPast();
                                $displayStatus =
                                    $sub->status === 'cancelled'
                                        ? 'cancelled'
                                        : ($isReallyExpired
                                            ? 'expired'
                                            : ($isReallyActive
                                                ? 'active'
                                                : $sub->status));
                                $statusColor = match ($displayStatus) {
                                    'active' => 'success',
                                    'expired' => 'warning',
                                    'cancelled' => 'error',
                                    default => 'ghost',
                                };
                                $planColor = match ($sub->plan->tier ?? '') {
                                    'free' => 'ghost',
                                    'premium' => 'info',
                                    'sultan' => 'warning',
                                    default => 'ghost',
                                };
                            @endphp
                            <tr class="border-b border-base-300/50 hover:bg-white/[0.02] transition-colors">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar avatar-placeholder shrink-0">
                                            <div class="bg-primary/20 rounded-full w-8 grid place-items-center">
                                                <span class="text-[10px] font-bold font-mono text-primary">
                                                    {{ strtoupper(substr($sub->user->name ?? '?', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-base-content truncate">
                                                {{ $sub->user->name ?? '-' }}</p>
                                            <p class="text-[11px] text-base-content/30 font-mono truncate">
                                                {{ $sub->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-sm badge-{{ $planColor }} badge-soft font-mono text-[10px]">
                                        {{ $sub->plan->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-sm badge-{{ $statusColor }} badge-soft font-mono text-[10px]">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="text-xs text-base-content/40 font-mono">
                                    {{ $sub->started_at?->format('d M Y') ?? '-' }}
                                </td>
                                <td
                                    class="text-xs font-mono {{ $isReallyExpired ? 'text-warning' : 'text-base-content/40' }}">
                                    {{ $sub->expired_at?->format('d M Y') ?? '∞' }}
                                    @if ($isReallyActive && $sub->expired_at)
                                        <span class="text-success/60 text-[10px] block">{{ $sub->daysRemaining() }}
                                            hari lagi</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-1 justify-end">
                                        @if ($displayStatus !== 'cancelled')
                                            <button
                                                onclick="openExtend({{ $sub->id }}, '{{ addslashes($sub->user->name) }}')"
                                                class="btn btn-ghost btn-xs text-primary/60 hover:text-primary">
                                                Extend
                                            </button>
                                            <form method="POST"
                                                action="{{ route('admin.subscriptions.cancel', $sub) }}"
                                                onsubmit="return confirm('Cancel subscription {{ $sub->user->name }}?')">
                                                @csrf @method('PATCH')
                                                <button
                                                    class="btn btn-ghost btn-xs text-error/50 hover:text-error">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <ul class="sm:hidden divide-y divide-base-300/50">
                @foreach ($subscriptions as $sub)
                    @php
                        $isReallyActive =
                            $sub->status === 'active' && (is_null($sub->expired_at) || $sub->expired_at->isFuture());
                        $isReallyExpired = $sub->status === 'active' && $sub->expired_at && $sub->expired_at->isPast();
                        $displayStatus =
                            $sub->status === 'cancelled'
                                ? 'cancelled'
                                : ($isReallyExpired
                                    ? 'expired'
                                    : ($isReallyActive
                                        ? 'active'
                                        : $sub->status));
                        $statusColor = match ($displayStatus) {
                            'active' => 'success',
                            'expired' => 'warning',
                            'cancelled' => 'error',
                            default => 'ghost',
                        };
                        $planColor = match ($sub->plan->tier ?? '') {
                            'free' => 'ghost',
                            'premium' => 'info',
                            'sultan' => 'warning',
                            default => 'ghost',
                        };
                    @endphp
                    <li class="p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="avatar avatar-placeholder shrink-0">
                                <div class="bg-primary/20 rounded-full w-8 grid place-items-center">
                                    <span
                                        class="text-[10px] font-bold font-mono text-primary">{{ strtoupper(substr($sub->user->name ?? '?', 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-base-content truncate">
                                    {{ $sub->user->name ?? '-' }}</p>
                                <p class="text-[11px] text-base-content/30 font-mono">
                                    {{ $sub->expired_at?->format('d M Y') ?? '∞' }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="badge badge-xs badge-{{ $planColor }} badge-soft font-mono">{{ $sub->plan->name ?? '-' }}</span>
                                <span
                                    class="badge badge-xs badge-{{ $statusColor }} badge-soft font-mono">{{ $displayStatus }}</span>
                            </div>
                        </div>
                        @if ($displayStatus !== 'cancelled')
                            <div class="flex gap-2 justify-end">
                                <button
                                    onclick="openExtend({{ $sub->id }}, '{{ addslashes($sub->user->name) }}')"
                                    class="btn btn-ghost btn-xs text-primary">Extend</button>
                                <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}"
                                    onsubmit="return confirm('Cancel?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-ghost btn-xs text-error">Cancel</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($subscriptions->hasPages())
        <div class="mt-6">{{ $subscriptions->links() }}</div>
    @endif

    {{-- ══════════ MODAL EXTEND ══════════ --}}
    <dialog id="modal-extend" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Perpanjang Subscription</h3>
            <p id="extend-label" class="text-sm text-base-content/50 mb-6"></p>
            <form id="form-extend" method="POST" class="flex flex-col gap-4">
                @csrf @method('PATCH')
                <x-ui.input name="days" type="number" label="Perpanjang (Hari)" placeholder="30" value="30"
                    :required="true" />
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Perpanjang</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL ASSIGN ══════════ --}}
    <dialog id="modal-assign" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Assign Paket Manual</h3>
            <p class="text-sm text-base-content/50 mb-6">Berikan paket langganan ke user tertentu.</p>
            <form method="POST" action="{{ route('admin.subscriptions.assign') }}" class="flex flex-col gap-4">
                @csrf
                <x-ui.input name="user_id" type="number" label="User ID" placeholder="Masukkan ID user"
                    :required="true" />
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Plan</label>
                    <select name="plan_id" required class="ft-select">
                        <option value="" disabled selected>Pilih paket</option>
                        @foreach ($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->formattedPrice() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-ui.input name="days" type="number" label="Durasi (Hari)" placeholder="30" value="30"
                    :required="true" />
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Assign</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    <script>
        function openExtend(id, name) {
            document.getElementById('form-extend').action = '/admin/subscriptions/' + id + '/extend'
            document.getElementById('extend-label').textContent = 'Perpanjang subscription untuk ' + name
            document.getElementById('modal-extend').showModal()
        }
    </script>

</x-layouts.core>
