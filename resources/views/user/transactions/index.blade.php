<x-layouts.core title="Transaksi">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Transaksi</h1>
            <p class="text-sm text-base-content/50 mt-1">Catatan pemasukan, pengeluaran, dan transfer.</p>
        </div>
        <button onclick="document.getElementById('modal-add').showModal()"
            class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah
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

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="rounded-xl bg-white/5 border border-base-300 p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-3.5 h-3.5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5" />
                        <polyline points="5 12 12 5 19 12" />
                    </svg>
                </div>
                <span class="text-[10px] font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Pemasukan
                </span>
            </div>
            <p class="text-lg sm:text-xl font-bold font-mono tracking-tight text-success">
                Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-base-content/35 font-mono mt-0.5">bulan ini</p>
        </div>

        <div class="rounded-xl bg-white/5 border border-base-300 p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-error/15 grid place-items-center">
                    <svg class="w-3.5 h-3.5 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <polyline points="19 12 12 19 5 12" />
                    </svg>
                </div>
                <span class="text-[10px] font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Pengeluaran
                </span>
            </div>
            <p class="text-lg sm:text-xl font-bold font-mono tracking-tight text-error">
                Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-base-content/35 font-mono mt-0.5">bulan ini</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-end gap-3 mb-6">

        {{-- Filter tipe --}}
        <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-base-300">
            @foreach (['all' => 'Semua', 'income' => 'Masuk', 'expense' => 'Keluar', 'transfer' => 'Transfer'] as $key => $label)
                <button type="submit" name="filter" value="{{ $key }}"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                               {{ $filter === $key
                                   ? ($key === 'income'
                                       ? 'bg-success/15 text-success'
                                       : ($key === 'expense'
                                           ? 'bg-error/15 text-error'
                                           : ($key === 'transfer'
                                               ? 'bg-info/15 text-info'
                                               : 'bg-primary/15 text-primary')))
                                   : 'text-base-content/50 hover:text-base-content' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Tanggal --}}
        <div class="flex items-center gap-2">
            <input type="date" name="from" value="{{ $from }}"
                class="ft-select !py-1.5 !px-2.5 !text-xs max-w-[140px]" />
            <span class="text-xs text-base-content/30">—</span>
            <input type="date" name="to" value="{{ $to }}"
                class="ft-select !py-1.5 !px-2.5 !text-xs max-w-[140px]" />
        </div>

        @if ($from || $to)
            <a href="{{ route('transactions.index', ['filter' => $filter]) }}"
                class="text-xs text-error/70 hover:text-error transition-colors font-medium">
                Reset
            </a>
        @endif
    </form>

    {{-- ══════════ TRANSACTION LIST ══════════ --}}
    <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">

        {{-- List: income & expense --}}
        @if ($filter !== 'transfer')
            @if (
                $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator
                    ? $transactions->isEmpty()
                    : $transactions->isEmpty())
                @if ($filter === 'all' && $transfers->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
                        <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        <p class="text-sm text-base-content/40 font-medium">Belum ada transaksi</p>
                        <p class="text-xs text-base-content/25 mt-1">Mulai catat keuanganmu sekarang</p>
                    </div>
                @else
                    <div class="py-10 text-center">
                        <p class="text-sm text-base-content/40">Tidak ada transaksi
                            {{ $filter === 'income' ? 'pemasukan' : 'pengeluaran' }} ditemukan</p>
                    </div>
                @endif
            @else
                <ul class="divide-y divide-base-300/50">
                    @foreach ($transactions as $trx)
                        <li onclick="openDetail({{ $trx->id }})"
                            class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 hover:bg-white/[0.02] transition-colors group cursor-pointer">

                            {{-- Icon --}}
                            <div
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg shrink-0 grid place-items-center
                                        {{ $trx->type === 'income' ? 'bg-success/10' : 'bg-error/10' }}">
                                <x-ui.icon :name="$trx->category->icon ?? 'ellipsis-horizontal'"
                                    class="w-4 h-4 sm:w-5 sm:h-5 {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}" />
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-base-content truncate">
                                    {{ $trx->note ?: $trx->category->name }}
                                </p>
                                <p class="text-[11px] text-base-content/35 mt-0.5 truncate font-mono">
                                    {{ $trx->category->name }} · {{ $trx->account->name }}
                                </p>
                            </div>

                            {{-- Amount & date --}}
                            <div class="text-right shrink-0">
                                <p
                                    class="text-sm font-semibold font-mono
                                          {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}">
                                    {{ $trx->type === 'income' ? '+' : '-' }}Rp
                                    {{ number_format($trx->amount, 0, ',', '.') }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <p class="text-[11px] text-base-content/30 font-mono">
                                        {{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->isoFormat('D MMM') }}
                                    </p>
                                    @if ($trx->attachments->isNotEmpty())
                                        <span class="text-base-content/25"
                                            title="{{ $trx->attachments->count() }} lampiran">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Chevron --}}
                            <svg class="w-4 h-4 text-base-content/15 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif

        {{-- List: transfers --}}
        @if ($filter === 'all' || $filter === 'transfer')
            @php $tfList = $filter === 'transfer' ? $transfers : $transfers; @endphp
            @if ($tfList->isNotEmpty())
                @if ($filter === 'all')
                    <div class="px-5 py-3 bg-white/[0.02] border-t border-base-300/50">
                        <p
                            class="text-[10px] font-mono uppercase tracking-[0.18em] text-base-content/30 font-semibold">
                            Transfer
                        </p>
                    </div>
                @endif
                <ul class="divide-y divide-base-300/50">
                    @foreach ($tfList as $tf)
                        <li
                            class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 hover:bg-white/[0.02] transition-colors group">

                            {{-- Icon --}}
                            <div
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-info/10 grid place-items-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-info" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="17 1 21 5 17 9" />
                                    <path d="M3 11V9a4 4 0 0 1 4-4h14" />
                                    <polyline points="7 23 3 19 7 15" />
                                    <path d="M21 13v2a4 4 0 0 1-4 4H3" />
                                </svg>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-base-content truncate">
                                    {{ $tf->note ?: 'Transfer' }}
                                </p>
                                <p class="text-[11px] text-base-content/35 mt-0.5 truncate font-mono">
                                    {{ $tf->fromAccount->name }} → {{ $tf->toAccount->name }}
                                </p>
                            </div>

                            {{-- Amount --}}
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold font-mono text-info">
                                    Rp {{ number_format($tf->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-base-content/30 mt-0.5 font-mono">
                                    {{ \Carbon\Carbon::parse($tf->transfer_date)->locale('id')->isoFormat('D MMM') }}
                                </p>
                            </div>

                            {{-- Delete --}}
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button"
                                    class="btn btn-ghost btn-xs btn-square text-base-content/20 hover:text-base-content opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="5" r="1" />
                                        <circle cx="12" cy="12" r="1" />
                                        <circle cx="12" cy="19" r="1" />
                                    </svg>
                                </div>
                                <ul tabindex="0"
                                    class="dropdown-content menu menu-sm z-50 mt-1 w-36 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
                                    <li>
                                        <form method="POST" action="{{ route('transfers.destroy', $tf) }}"
                                            onsubmit="return confirm('Yakin hapus transfer ini?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="rounded-lg text-sm text-error w-full text-left flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @elseif ($filter === 'transfer')
                <div class="py-10 text-center">
                    <p class="text-sm text-base-content/40">Belum ada transfer</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Pagination --}}
    @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @endif
    @if (
        $filter === 'transfer' &&
            $transfers instanceof \Illuminate\Pagination\LengthAwarePaginator &&
            $transfers->hasPages())
        <div class="mt-6">
            {{ $transfers->links() }}
        </div>
    @endif

    {{-- ══════════ MODAL TAMBAH ══════════ --}}
    <dialog id="modal-add" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300 max-w-md">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Transaksi</h3>
            <p class="text-sm text-base-content/50 mb-5">Catat pemasukan, pengeluaran, atau transfer.</p>

            {{-- Tab tipe --}}
            <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-base-300 mb-6">
                <button type="button" onclick="switchFormTab('expense')" id="form-tab-expense"
                    class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all bg-error/15 text-error text-center">
                    Pengeluaran
                </button>
                <button type="button" onclick="switchFormTab('income')" id="form-tab-income"
                    class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all text-base-content/50 text-center">
                    Pemasukan
                </button>
                <button type="button" onclick="switchFormTab('transfer')" id="form-tab-transfer"
                    class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all text-base-content/50 text-center">
                    Transfer
                </button>
            </div>

            {{-- ── Form: Income / Expense ── --}}
            <form id="form-tx" method="POST" action="{{ route('transactions.store') }}"
                enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="transaction_type" value="expense" id="tx-type-hidden" />
                <input type="hidden" name="type" value="expense" id="tx-type-value" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Akun
                    </label>
                    <select name="account_id" required class="ft-select">
                        <option value="" disabled selected>Pilih akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5" id="category-group">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Kategori
                    </label>
                    <select name="category_id" required class="ft-select" id="select-category">
                        <option value="" disabled selected>Pilih kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-ui.input name="amount" type="number" label="Nominal" placeholder="0" :required="true" />

                <x-ui.input name="transaction_date" type="date" label="Tanggal" :required="true"
                    value="{{ now()->format('Y-m-d') }}" />

                <x-ui.input name="note" label="Catatan" placeholder="Opsional" />

                {{-- Attachment --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Lampiran <span class="text-base-content/30">(Opsional · Maks 5 file)</span>
                    </label>
                    <label
                        class="flex flex-col items-center gap-2 p-4 border-2 border-dashed border-base-300 rounded-xl
                                  cursor-pointer hover:border-primary/40 transition-colors bg-white/[0.02]">
                        <svg class="w-6 h-6 text-base-content/25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                        </svg>
                        <span class="text-xs text-base-content/40 text-center">
                            Klik untuk upload foto atau dokumen
                        </span>
                        <span class="text-[10px] text-base-content/25 font-mono">JPG, PNG, PDF · Maks 2MB</span>
                        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden" onchange="showFileNames(this, 'file-list-tx')" />
                    </label>
                    <div id="file-list-tx" class="flex flex-wrap gap-1.5"></div>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold" id="btn-submit-tx">
                    Simpan Pengeluaran
                </button>
            </form>

            {{-- ── Form: Transfer ── --}}
            <form id="form-tf" method="POST" action="{{ route('transactions.store') }}"
                class="hidden flex flex-col gap-4">
                @csrf
                <input type="hidden" name="transaction_type" value="transfer" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Dari Akun
                    </label>
                    <select name="from_account_id" required class="ft-select">
                        <option value="" disabled selected>Akun asal</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Ke Akun
                    </label>
                    <select name="to_account_id" required class="ft-select">
                        <option value="" disabled selected>Akun tujuan</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <x-ui.input name="amount" type="number" label="Nominal" placeholder="0" :required="true" />

                <x-ui.input name="transfer_date" type="date" label="Tanggal" :required="true"
                    value="{{ now()->format('Y-m-d') }}" />

                <x-ui.input name="note" label="Catatan" placeholder="Opsional" />

                <button type="submit" class="btn btn-info w-full mt-2 font-semibold">
                    Simpan Transfer
                </button>
            </form>

        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>


    {{-- ══════════ MODAL DETAIL ══════════ --}}
    <dialog id="modal-detail" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300 max-w-md p-0">
            <form method="dialog">
                <button
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40 z-10">✕</button>
            </form>

            {{-- Loading --}}
            <div id="detail-loading" class="flex items-center justify-center py-20">
                <span class="loading loading-spinner loading-md text-primary"></span>
            </div>

            {{-- Content (hidden until loaded) --}}
            <div id="detail-content" class="hidden">

                {{-- Header with type color --}}
                <div id="detail-header" class="px-6 pt-6 pb-4 border-b border-base-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div id="detail-icon-box" class="w-11 h-11 rounded-xl grid place-items-center">
                            <span id="detail-icon"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p id="detail-category" class="text-sm font-semibold text-base-content"></p>
                            <p id="detail-account" class="text-[11px] font-mono text-base-content/40"></p>
                        </div>
                        <span id="detail-type-badge"
                            class="badge badge-sm badge-soft font-mono text-[10px] font-semibold"></span>
                    </div>
                    <p id="detail-amount" class="text-2xl font-bold font-mono tracking-tight"></p>
                </div>

                {{-- Info rows --}}
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-base-content/40 font-mono uppercase tracking-widest">Tanggal</span>
                        <span id="detail-date" class="text-sm text-base-content font-medium"></span>
                    </div>
                    <div class="flex items-center justify-between" id="detail-note-row">
                        <span class="text-xs text-base-content/40 font-mono uppercase tracking-widest">Catatan</span>
                        <span id="detail-note"
                            class="text-sm text-base-content/70 text-right max-w-[60%] truncate"></span>
                    </div>
                </div>

                {{-- Attachments --}}
                <div id="detail-attachments-section" class="hidden px-6 pb-4">
                    <p
                        class="text-[10px] font-mono uppercase tracking-[0.18em] text-base-content/30 font-semibold mb-3">
                        Lampiran
                    </p>
                    <div id="detail-attachments" class="grid grid-cols-2 gap-2"></div>
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 border-t border-base-300 flex gap-3">
                    <button onclick="openEditFromDetail()" class="btn btn-ghost btn-sm flex-1 font-semibold gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Edit
                    </button>
                    <form id="detail-delete-form" method="POST" class="flex-1"
                        onsubmit="return confirm('Yakin hapus transaksi ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-error btn-sm btn-soft w-full font-semibold gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL EDIT ══════════ --}}
    <dialog id="modal-edit" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300 max-w-md">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Transaksi</h3>
            <p class="text-sm text-base-content/50 mb-5">Perbarui detail transaksimu.</p>

            <form id="form-edit-tx" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <input type="hidden" name="type" id="edit-tx-type" />

                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Akun</label>
                    <select name="account_id" id="edit-account" required class="ft-select">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Kategori</label>
                    <select name="category_id" id="edit-category" required class="ft-select">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-ui.input name="amount" type="number" label="Nominal" placeholder="0" :required="true"
                    value="" />
                <x-ui.input name="transaction_date" type="date" label="Tanggal" :required="true"
                    value="" />
                <x-ui.input name="note" label="Catatan" placeholder="Opsional" value="" />

                {{-- Existing attachments --}}
                <div id="edit-existing-attachments" class="hidden">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono mb-2 block">
                        Lampiran Saat Ini
                    </label>
                    <div id="edit-att-list" class="space-y-2"></div>
                </div>

                {{-- Upload new --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Tambah Lampiran <span class="text-base-content/30">(Opsional)</span>
                    </label>
                    <label
                        class="flex flex-col items-center gap-2 p-3 border-2 border-dashed border-base-300 rounded-xl
                                  cursor-pointer hover:border-primary/40 transition-colors bg-white/[0.02]">
                        <svg class="w-5 h-5 text-base-content/25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                        </svg>
                        <span class="text-[10px] text-base-content/30 font-mono">JPG, PNG, PDF · Maks 2MB</span>
                        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden" onchange="showFileNames(this, 'file-list-edit')" />
                    </label>
                    <div id="file-list-edit" class="flex flex-wrap gap-1.5"></div>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Perubahan</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>


    {{-- ══════════ MODAL PREVIEW ATTACHMENT ══════════ --}}
    <dialog id="modal-preview" class="modal" style="z-index:100;">
        <div class="modal-box bg-base-200 border border-base-300 max-w-3xl w-[95vw] max-h-[90vh] p-0 flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-base-300 shrink-0">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg class="w-4 h-4 text-base-content/40 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                    </svg>
                    <span id="preview-filename" class="text-sm font-mono text-base-content/60 truncate"></span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="preview-download" href="#" download
                        class="btn btn-primary btn-sm font-semibold gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        Download
                    </a>
                    <form method="dialog">
                        <button class="btn btn-ghost btn-sm btn-square text-base-content/40">✕</button>
                    </form>
                </div>
            </div>

            {{-- Preview content --}}
            <div class="flex-1 overflow-auto flex items-center justify-center p-4 bg-black/20 min-h-0">
                <img id="preview-image" src="" alt=""
                    class="hidden max-w-full max-h-[70vh] rounded-lg object-contain" />
                <iframe id="preview-pdf" src="" class="hidden w-full h-[70vh] rounded-lg border-0"></iframe>
            </div>

        </div>
        <form method="dialog" class="modal-backdrop bg-black/70"><button>tutup</button></form>
    </dialog>

    <script>
        const tabStyles = {
            expense: 'bg-error/15 text-error',
            income: 'bg-success/15 text-success',
            transfer: 'bg-info/15 text-info',
        }

        function switchFormTab(tab) {
            const formTx = document.getElementById('form-tx')
            const formTf = document.getElementById('form-tf')
            const catGrp = document.getElementById('category-group')
            const btnSub = document.getElementById('btn-submit-tx')
            const selectCat = document.getElementById('select-category')

            // Reset all tab buttons
            ;
            ['expense', 'income', 'transfer'].forEach(t => {
                const btn = document.getElementById('form-tab-' + t)
                btn.className =
                    'flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all text-center text-base-content/50'
            })

            // Activate tab
            const activeBtn = document.getElementById('form-tab-' + tab)
            activeBtn.classList.add(...tabStyles[tab].split(' '))

            if (tab === 'transfer') {
                formTx.classList.add('hidden')
                formTf.classList.remove('hidden')
            } else {
                formTx.classList.remove('hidden')
                formTf.classList.add('hidden')

                document.getElementById('tx-type-hidden').value = tab
                document.getElementById('tx-type-value').value = tab

                // Filter kategori berdasarkan type
                const opts = selectCat.options
                for (let i = 0; i < opts.length; i++) {
                    if (opts[i].dataset.type) {
                        opts[i].hidden = opts[i].dataset.type !== tab
                    }
                }
                selectCat.value = ''

                btnSub.textContent = tab === 'income' ? 'Simpan Pemasukan' : 'Simpan Pengeluaran'
                btnSub.className = 'btn w-full mt-2 font-semibold ' + (tab === 'income' ? 'btn-success' : 'btn-primary')
            }
        }

        // Show selected file names
        function showFileNames(input, containerId) {
            const container = document.getElementById(containerId)
            container.innerHTML = ''
            const files = Array.from(input.files)

            if (files.length > 5) {
                input.value = ''
                container.innerHTML = '<span class="text-xs text-error">Maksimal 5 file</span>'
                return
            }

            files.forEach(f => {
                const tag = document.createElement('span')
                tag.className =
                    'inline-flex items-center gap-1 px-2 py-1 rounded-md bg-primary/10 text-[11px] font-mono text-primary'
                tag.innerHTML =
                    '<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>' +
                    f.name.substring(0, 20) + (f.name.length > 20 ? '...' : '')
                container.appendChild(tag)
            })
        }

        // ── Detail modal ──
        let currentTxData = null

        async function openDetail(id) {
            const modal = document.getElementById('modal-detail')
            document.getElementById('detail-loading').classList.remove('hidden')
            document.getElementById('detail-content').classList.add('hidden')
            modal.showModal()

            try {
                const res = await fetch('/transactions/' + id)
                const data = await res.json()
                currentTxData = data

                // Type styling
                const isIncome = data.type === 'income'
                const color = isIncome ? 'success' : 'error'
                const label = isIncome ? 'Pemasukan' : 'Pengeluaran'

                document.getElementById('detail-icon-box').className =
                    'w-11 h-11 rounded-xl grid place-items-center bg-' + color + '/10'
                document.getElementById('detail-icon').innerHTML = '<svg class="w-5 h-5 text-' + color +
                    '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="' +
                    (isIncome ? '19' : '5') + '" x2="12" y2="' + (isIncome ? '5' : '19') + '"/><polyline points="' + (
                        isIncome ? '5 12 12 5 19 12' : '19 12 12 19 5 12') + '"/></svg>'

                document.getElementById('detail-category').textContent = data.category.name
                document.getElementById('detail-account').textContent = data.account.name

                const badge = document.getElementById('detail-type-badge')
                badge.textContent = label
                badge.className = 'badge badge-sm badge-soft font-mono text-[10px] font-semibold badge-' + color

                const amountEl = document.getElementById('detail-amount')
                amountEl.textContent = (isIncome ? '+' : '-') + 'Rp ' + Number(data.amount).toLocaleString('id-ID')
                amountEl.className = 'text-2xl font-bold font-mono tracking-tight text-' + color

                document.getElementById('detail-date').textContent = new Date(data.transaction_date).toLocaleDateString(
                    'id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    })

                const noteRow = document.getElementById('detail-note-row')
                if (data.note) {
                    noteRow.classList.remove('hidden')
                    document.getElementById('detail-note').textContent = data.note
                } else {
                    noteRow.classList.add('hidden')
                }

                // Attachments
                const attSection = document.getElementById('detail-attachments-section')
                const attContainer = document.getElementById('detail-attachments')
                attContainer.innerHTML = ''

                if (data.attachments.length > 0) {
                    attSection.classList.remove('hidden')
                    data.attachments.forEach(att => {
                        const el = document.createElement('div')
                        if (att.is_image) {
                            el.className =
                                'relative rounded-lg overflow-hidden border border-base-300 group/att cursor-pointer'
                            el.onclick = () => openPreview(att.url, att.filename, true)
                            el.innerHTML = `
                                <img src="${att.url}" alt="${att.filename}" class="w-full h-28 object-cover" />
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover/att:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                        <line x1="11" y1="8" x2="11" y2="14"/>
                                        <line x1="8" y1="11" x2="14" y2="11"/>
                                    </svg>
                                </div>`
                        } else {
                            el.className =
                                'rounded-lg border border-base-300 p-3 flex items-center gap-2 bg-white/[0.02] cursor-pointer hover:bg-white/[0.04] transition-colors'
                            el.onclick = () => openPreview(att.url, att.filename, false)
                            el.innerHTML = `
                                <svg class="w-5 h-5 text-error/60 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <span class="text-xs text-base-content/60 truncate flex-1 font-mono">${att.filename}</span>
                                <span class="text-[10px] text-primary/60 font-mono shrink-0">Lihat</span>`
                        }
                        attContainer.appendChild(el)
                    })
                } else {
                    attSection.classList.add('hidden')
                }

                // Delete form action
                document.getElementById('detail-delete-form').action = '/transactions/' + data.id

                document.getElementById('detail-loading').classList.add('hidden')
                document.getElementById('detail-content').classList.remove('hidden')
            } catch (err) {
                console.error(err)
                document.getElementById('modal-detail').close()
            }
        }

        // ── Edit from detail ──
        function openEditFromDetail() {
            if (!currentTxData) return
            const d = currentTxData
            document.getElementById('modal-detail').close()

            const form = document.getElementById('form-edit-tx')
            form.action = '/transactions/' + d.id

            document.getElementById('edit-tx-type').value = d.type
            document.getElementById('edit-account').value = d.account.id

            // Filter category options
            const catSelect = document.getElementById('edit-category')
            for (let i = 0; i < catSelect.options.length; i++) {
                const opt = catSelect.options[i]
                if (opt.dataset.type) opt.hidden = opt.dataset.type !== d.type
            }
            catSelect.value = d.category.id

            form.querySelector('[name="amount"]').value = d.amount
            form.querySelector('[name="transaction_date"]').value = d.transaction_date
            form.querySelector('[name="note"]').value = d.note || ''

            // Show existing attachments
            const attSection = document.getElementById('edit-existing-attachments')
            const attList = document.getElementById('edit-att-list')
            attList.innerHTML = ''

            if (d.attachments.length > 0) {
                attSection.classList.remove('hidden')
                d.attachments.forEach(att => {
                    const row = document.createElement('div')
                    row.className = 'flex items-center gap-2 p-2 rounded-lg bg-white/[0.02] border border-base-300'
                    row.innerHTML = `
                        ${att.is_image
                            ? `<img src="${att.url}" class="w-10 h-10 rounded object-cover shrink-0" />`
                            : `<div class="w-10 h-10 rounded bg-error/10 grid place-items-center shrink-0"><svg class="w-4 h-4 text-error/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>`
                        }
                        <span class="text-xs text-base-content/60 truncate flex-1 font-mono">${att.filename}</span>
                        <label class="flex items-center gap-1.5 cursor-pointer shrink-0">
                            <input type="checkbox" name="delete_attachments[]" value="${att.id}" class="checkbox checkbox-error checkbox-xs" />
                            <span class="text-[10px] text-error/60 font-mono">Hapus</span>
                        </label>`
                    attList.appendChild(row)
                })
            } else {
                attSection.classList.add('hidden')
            }

            document.getElementById('modal-edit').showModal()
        }

        // ── Preview modal ──
        function openPreview(url, filename, isImage) {
            const imgEl = document.getElementById('preview-image')
            const pdfEl = document.getElementById('preview-pdf')
            const dlBtn = document.getElementById('preview-download')

            document.getElementById('preview-filename').textContent = filename
            dlBtn.href = url
            dlBtn.download = filename

            if (isImage) {
                imgEl.src = url
                imgEl.alt = filename
                imgEl.classList.remove('hidden')
                pdfEl.classList.add('hidden')
                pdfEl.src = ''
            } else {
                pdfEl.src = url
                pdfEl.classList.remove('hidden')
                imgEl.classList.add('hidden')
                imgEl.src = ''
            }

            document.getElementById('modal-preview').showModal()
        }

        // Cleanup saat preview ditutup
        document.getElementById('modal-preview')?.addEventListener('close', () => {
            document.getElementById('preview-image').src = ''
            document.getElementById('preview-pdf').src = ''
        })

        // Initialize: filter categories on load
        switchFormTab('expense')
    </script>

</x-layouts.core>
