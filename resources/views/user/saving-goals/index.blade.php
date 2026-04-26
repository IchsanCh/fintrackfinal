<x-layouts.core title="Tabungan">

    @php
        $overallPct = $totalTarget > 0 ? (int) round(($totalSaved / $totalTarget) * 100) : 0;
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Tabungan</h1>
            <p class="text-sm text-base-content/50 mt-1">Atur target dan pantau progressmu.</p>
        </div>
        @if ($tierSummary['can_add_saving'])
            <button onclick="document.getElementById('modal-add').showModal()"
                class="btn btn-primary btn-sm font-semibold gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Target
            </button>
        @else
            <div class="tooltip tooltip-left" data-tip="Batas tabungan paket {{ $tierSummary['plan']->name }} tercapai">
                <button class="btn btn-primary btn-sm font-semibold gap-2 btn-disabled" disabled>
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah Target
                </button>
            </div>
        @endif
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
    @if ($activeGoals->isNotEmpty())
        <div class="rounded-xl bg-white/5 border border-base-300 p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <p class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold mb-1">Total
                        Terkumpul</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                            Rp {{ number_format($totalSaved, 0, ',', '.') }}
                        </p>
                        <span class="text-sm text-base-content/40 font-mono">
                            / Rp {{ number_format($totalTarget, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold font-mono text-primary">{{ $overallPct }}%</span>
                    @if (!$tierSummary['plan']->isUnlimited('max_saving_goals'))
                        <span class="badge badge-sm badge-primary badge-soft font-mono text-[10px]">
                            {{ $activeGoals->count() }}/{{ $tierSummary['plan']->max_saving_goals }} ·
                            {{ $tierSummary['plan']->name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="w-full h-3 rounded-full bg-base-300 overflow-hidden">
                <div class="h-full rounded-full bg-primary transition-all duration-500"
                    style="width: {{ min($overallPct, 100) }}%"></div>
            </div>
        </div>
    @endif

    {{-- Active goals --}}
    @if ($activeGoals->isEmpty() && $achievedGoals->isEmpty() && $completedGoals->isEmpty() && $cancelledGoals->isEmpty())
        <div
            class="rounded-xl bg-white/5 border border-base-300 flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
            <p class="text-sm text-base-content/40 font-medium">Belum ada target tabungan</p>
            <p class="text-xs text-base-content/25 mt-1 mb-4">Mulai menabung untuk impianmu</p>
            <button onclick="document.getElementById('modal-add').showModal()"
                class="btn btn-primary btn-sm font-semibold">Tambah Target</button>
        </div>
    @else
        {{-- Active --}}
        @if ($activeGoals->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
                @foreach ($activeGoals as $goal)
                    @php
                        $color =
                            $goal->percentage >= 100 ? 'success' : ($goal->percentage >= 70 ? 'primary' : 'primary');
                    @endphp
                    <div
                        class="rounded-xl bg-white/5 border border-base-300 overflow-hidden hover:border-primary/30 transition-colors">
                        {{-- Header --}}
                        <div class="p-5 pb-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-base font-semibold text-base-content truncate">{{ $goal->title }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="badge badge-sm badge-primary badge-soft font-mono text-[10px]">Aktif</span>
                                        @if ($goal->days_left !== null)
                                            <span class="text-[11px] text-base-content/35 font-mono">
                                                {{ $goal->days_left > 0 ? $goal->days_left . ' hari lagi' : 'Melewati deadline' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button"
                                        class="btn btn-ghost btn-xs btn-square text-base-content/25 hover:text-base-content">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="5" r="1" />
                                            <circle cx="12" cy="12" r="1" />
                                            <circle cx="12" cy="19" r="1" />
                                        </svg>
                                    </div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu menu-sm z-50 mt-1 w-40 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
                                        <li><button
                                                onclick="openEditGoal({{ $goal->id }}, '{{ addslashes($goal->title) }}', {{ $goal->target_amount }}, '{{ $goal->deadline }}')"
                                                class="rounded-lg text-sm">Edit</button></li>
                                        <li><button onclick="openHistory({{ $goal->id }})"
                                                class="rounded-lg text-sm">Riwayat</button></li>
                                        <li>
                                            <button type="button"
                                                onclick="if(confirm('Yakin batalkan target ini?')){document.getElementById('cancel-form-{{ $goal->id }}').submit()}"
                                                class="rounded-lg text-sm text-warning flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="15" y1="9" x2="9"
                                                        y2="15" />
                                                    <line x1="9" y1="9" x2="15"
                                                        y2="15" />
                                                </svg>
                                                Batalkan
                                            </button>
                                            <form id="cancel-form-{{ $goal->id }}" method="POST"
                                                action="{{ route('saving-goals.cancel', $goal) }}" class="hidden">
                                                @csrf @method('PATCH')
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Progress --}}
                            <div class="mb-3">
                                <div class="flex items-baseline justify-between mb-1.5">
                                    <p class="text-xs font-mono text-base-content/50">
                                        Rp {{ number_format($goal->current_amount, 0, ',', '.') }}
                                        <span class="text-base-content/25">/ Rp
                                            {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                    </p>
                                    <span
                                        class="text-sm font-bold font-mono text-{{ $color }}">{{ $goal->percentage }}%</span>
                                </div>
                                <div class="w-full h-2.5 rounded-full bg-base-300 overflow-hidden">
                                    <div class="h-full rounded-full bg-{{ $color }} transition-all duration-500"
                                        style="width: {{ min($goal->percentage, 100) }}%"></div>
                                </div>
                                <p class="text-[11px] text-base-content/30 font-mono mt-1.5">
                                    Sisa Rp {{ number_format($goal->remaining, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex border-t border-base-300 divide-x divide-base-300">
                            <button onclick="openDeposit({{ $goal->id }}, '{{ addslashes($goal->title) }}')"
                                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold text-success hover:bg-success/5 transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="19" x2="12" y2="5" />
                                    <polyline points="5 12 12 5 19 12" />
                                </svg>
                                Deposit
                            </button>
                            <button
                                onclick="openWithdraw({{ $goal->id }}, '{{ addslashes($goal->title) }}', {{ $goal->current_amount }})"
                                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold text-error hover:bg-error/5 transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <polyline points="19 12 12 19 5 12" />
                                </svg>
                                Tarik
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Achieved — siap dicairkan --}}
        @if ($achievedGoals->isNotEmpty())
            <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-base-content/30 font-semibold mb-3">
                Tercapai — Siap Dicairkan
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-8">
                @foreach ($achievedGoals as $goal)
                    <div class="rounded-xl bg-white/5 border border-success/20 overflow-hidden">
                        <div class="p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-success/10 grid place-items-center shrink-0">
                                <svg class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-base-content truncate">{{ $goal->title }}</p>
                                <p class="text-xs text-success font-mono">Rp
                                    {{ number_format($goal->current_amount, 0, ',', '.') }} siap dicairkan</p>
                            </div>
                            <button onclick="openHistory({{ $goal->id }})"
                                class="btn btn-ghost btn-xs text-base-content/30 hover:text-base-content">
                                Riwayat
                            </button>
                        </div>
                        <div class="border-t border-success/10 px-4 py-2.5 flex justify-end">
                            <button
                                onclick="openCashout({{ $goal->id }}, '{{ addslashes($goal->title) }}', {{ $goal->current_amount }})"
                                class="btn btn-success btn-sm font-semibold gap-1.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Selesai & Cairkan
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Completed — riwayat pencapaian --}}
        @if ($completedGoals->isNotEmpty())
            <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-base-content/30 font-semibold mb-3">
                Selesai
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-8">
                @foreach ($completedGoals as $goal)
                    <div class="rounded-xl bg-white/5 border border-base-300 p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-success/10 grid place-items-center shrink-0">
                            <svg class="w-5 h-5 text-success/60" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-base-content/70 truncate">{{ $goal->title }}</p>
                            <p class="text-xs text-base-content/30 font-mono">Rp
                                {{ number_format($goal->target_amount, 0, ',', '.') }} · Selesai</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button onclick="openHistory({{ $goal->id }})"
                                class="btn btn-ghost btn-xs text-base-content/30 hover:text-base-content">
                                Riwayat
                            </button>
                            <form method="POST" action="{{ route('saving-goals.destroy', $goal) }}"
                                onsubmit="return confirm('Hapus riwayat tabungan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-xs text-error/40 hover:text-error">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Cancelled --}}
        @if ($cancelledGoals->isNotEmpty())
            <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-base-content/30 font-semibold mb-3">
                Dibatalkan
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-8">
                @foreach ($cancelledGoals as $goal)
                    <div class="rounded-xl bg-white/5 border border-base-300 p-4 flex items-center gap-4 opacity-50">
                        <div class="w-10 h-10 rounded-lg bg-base-300 grid place-items-center shrink-0">
                            <svg class="w-5 h-5 text-base-content/30" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="15" y1="9" x2="9" y2="15" />
                                <line x1="9" y1="9" x2="15" y2="15" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-base-content truncate">{{ $goal->title }}</p>
                            <p class="text-xs text-base-content/30 font-mono">Dibatalkan</p>
                        </div>
                        <form method="POST" action="{{ route('saving-goals.destroy', $goal) }}"
                            onsubmit="return confirm('Hapus permanen?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-xs text-error/50 hover:text-error">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- ══════════ MODAL TAMBAH ══════════ --}}
    <dialog id="modal-add" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Target Tabungan</h3>
            <p class="text-sm text-base-content/50 mb-6">Tentukan target dan mulai menabung.</p>
            <form method="POST" action="{{ route('saving-goals.store') }}" class="flex flex-col gap-4">
                @csrf
                <x-ui.input name="title" label="Nama Target" placeholder="Contoh: iPhone 16, Dana Darurat"
                    :required="true" />
                <x-ui.input name="target_amount" type="number" label="Target (Rp)" placeholder="1000000"
                    :required="true" />
                <x-ui.input name="deadline" type="date" label="Deadline (Opsional)" />
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Buat Target</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL EDIT ══════════ --}}
    <dialog id="modal-edit" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Target</h3>
            <p class="text-sm text-base-content/50 mb-6">Perbarui detail target tabunganmu.</p>
            <form id="form-edit-goal" method="POST" class="flex flex-col gap-4">
                @csrf @method('PUT')
                <x-ui.input name="title" label="Nama Target" :required="true" value="" />
                <x-ui.input name="target_amount" type="number" label="Target (Rp)" :required="true"
                    value="" />
                <x-ui.input name="deadline" type="date" label="Deadline (Opsional)" value="" />
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Perubahan</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL DEPOSIT ══════════ --}}
    <dialog id="modal-deposit" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Deposit ke Tabungan</h3>
            <p id="deposit-label" class="text-sm text-base-content/50 mb-6"></p>
            <form id="form-deposit" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Dari
                        Akun</label>
                    <select name="account_id" required class="ft-select">
                        <option value="" disabled selected>Pilih akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <x-ui.input name="amount" type="number" label="Nominal (Rp)" placeholder="0" :required="true" />
                <x-ui.input name="note" label="Catatan" placeholder="Opsional" />
                <button type="submit" class="btn btn-success w-full mt-2 font-semibold">Deposit</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL WITHDRAW ══════════ --}}
    <dialog id="modal-withdraw" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Tarik dari Tabungan</h3>
            <p id="withdraw-label" class="text-sm text-base-content/50 mb-6"></p>
            <form id="form-withdraw" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Ke
                        Akun</label>
                    <select name="account_id" required class="ft-select">
                        <option value="" disabled selected>Pilih akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-ui.input name="amount" type="number" label="Nominal (Rp)" placeholder="0" :required="true" />
                <x-ui.input name="note" label="Catatan" placeholder="Opsional" />
                <button type="submit" class="btn btn-error w-full mt-2 font-semibold">Tarik Dana</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL RIWAYAT ══════════ --}}
    <dialog id="modal-history" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300 max-w-md p-0">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40 z-10">✕</button>
            </form>

            <div class="px-6 pt-6 pb-4 border-b border-base-300">
                <h3 class="text-lg font-semibold text-base-content">Riwayat Tabungan</h3>
                <p id="history-title" class="text-sm text-base-content/50"></p>
            </div>

            <div id="history-loading" class="flex items-center justify-center py-16">
                <span class="loading loading-spinner loading-md text-primary"></span>
            </div>

            <div id="history-content" class="hidden max-h-[60vh] overflow-y-auto">
                <div id="history-empty" class="hidden py-12 text-center">
                    <p class="text-sm text-base-content/40">Belum ada riwayat</p>
                </div>
                <ul id="history-list" class="divide-y divide-base-300/50"></ul>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL CASHOUT ══════════ --}}
    <dialog id="modal-cashout" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-base-content">Selesai & Cairkan</h3>
                    <p id="cashout-label" class="text-sm text-base-content/50"></p>
                </div>
            </div>

            <p class="text-sm text-base-content/60 mb-1">Semua dana akan dipindahkan ke akun yang kamu pilih.</p>
            <p id="cashout-amount" class="text-xl font-bold font-mono text-success mb-6"></p>

            <form id="form-cashout" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Cairkan
                        ke Akun</label>
                    <select name="account_id" required class="ft-select">
                        <option value="" disabled selected>Pilih akun tujuan</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-full mt-2 font-semibold">Cairkan Sekarang</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    <script>
        function openEditGoal(id, title, target, deadline) {
            const form = document.getElementById('form-edit-goal')
            form.action = '/saving-goals/' + id
            form.querySelector('[name="title"]').value = title
            form.querySelector('[name="target_amount"]').value = target
            form.querySelector('[name="deadline"]').value = deadline || ''
            document.getElementById('modal-edit').showModal()
        }

        function openCashout(id, title, amount) {
            document.getElementById('form-cashout').action = '/saving-goals/' + id + '/cashout'
            document.getElementById('cashout-label').textContent = title
            document.getElementById('cashout-amount').textContent = 'Rp ' + Number(amount).toLocaleString('id-ID')
            document.getElementById('modal-cashout').showModal()
        }

        function openDeposit(id, title) {
            document.getElementById('form-deposit').action = '/saving-goals/' + id + '/deposit'
            document.getElementById('deposit-label').textContent = 'Tambah dana ke "' + title + '"'
            document.getElementById('modal-deposit').showModal()
        }

        function openWithdraw(id, title, maxAmount) {
            document.getElementById('form-withdraw').action = '/saving-goals/' + id + '/withdraw'
            document.getElementById('withdraw-label').textContent = 'Tarik dana dari "' + title + '" (maks Rp ' + Number(
                maxAmount).toLocaleString('id-ID') + ')'
            document.getElementById('modal-withdraw').showModal()
        }

        async function openHistory(id) {
            const modal = document.getElementById('modal-history')
            document.getElementById('history-loading').classList.remove('hidden')
            document.getElementById('history-content').classList.add('hidden')
            modal.showModal()

            try {
                const res = await fetch('/saving-goals/' + id + '/history')
                const data = await res.json()

                document.getElementById('history-title').textContent = data.goal.title

                const list = document.getElementById('history-list')
                const empty = document.getElementById('history-empty')
                list.innerHTML = ''

                if (data.transactions.length === 0) {
                    empty.classList.remove('hidden')
                } else {
                    empty.classList.add('hidden')
                    data.transactions.forEach(t => {
                        const isDeposit = t.type === 'deposit'
                        const li = document.createElement('li')
                        li.className = 'flex items-center gap-3 px-6 py-3.5'
                        li.innerHTML = `
                            <div class="w-8 h-8 rounded-lg ${isDeposit ? 'bg-success/10' : 'bg-error/10'} grid place-items-center shrink-0">
                                <svg class="w-4 h-4 ${isDeposit ? 'text-success' : 'text-error'}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="${isDeposit ? '19' : '5'}" x2="12" y2="${isDeposit ? '5' : '19'}"/>
                                    <polyline points="${isDeposit ? '5 12 12 5 19 12' : '19 12 12 19 5 12'}"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-base-content">${t.note || (isDeposit ? 'Deposit' : 'Penarikan')}</p>
                                <p class="text-[11px] text-base-content/35 font-mono">${t.account} · ${t.created_at}</p>
                            </div>
                            <p class="text-sm font-semibold font-mono ${isDeposit ? 'text-success' : 'text-error'} shrink-0">
                                ${isDeposit ? '+' : '-'}Rp ${Number(t.amount).toLocaleString('id-ID')}
                            </p>`
                        list.appendChild(li)
                    })
                }

                document.getElementById('history-loading').classList.add('hidden')
                document.getElementById('history-content').classList.remove('hidden')
            } catch (err) {
                console.error(err)
                modal.close()
            }
        }
    </script>

</x-layouts.core>
