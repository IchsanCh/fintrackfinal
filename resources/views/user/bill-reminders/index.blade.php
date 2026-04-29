<x-layouts.core title="Tagihan">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Tagihan</h1>
            <p class="text-sm text-base-content/50 mt-1">Pantau dan bayar tagihan rutinmu.</p>
        </div>
        <button onclick="document.getElementById('modal-add').showModal()"
                class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Tagihan
        </button>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-6"><x-ui.alert type="success">{{ session('success') }}</x-ui.alert></div>
    @endif
    @if ($errors->any())
        <div class="mb-6"><x-ui.alert type="error">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </x-ui.alert></div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-error">{{ $overdue }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Terlambat</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-warning">{{ $dueToday }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Hari Ini</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-info">{{ $upcoming }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Mendatang</p>
        </div>
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 text-center">
            <p class="text-2xl font-bold font-mono text-success">{{ $paid }}</p>
            <p class="text-[11px] text-base-content/40 font-mono mt-0.5">Lunas</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex gap-1 p-1 bg-white/5 rounded-lg w-fit border border-base-300 mb-6">
        @foreach (['all' => 'Semua', 'overdue' => 'Terlambat', 'due_today' => 'Hari Ini', 'upcoming' => 'Mendatang', 'paid' => 'Lunas'] as $key => $label)
            <a href="{{ route('bill-reminders.index', ['filter' => $key]) }}"
               class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                      {{ $filter === $key
                          ? ($key === 'overdue' ? 'bg-error/15 text-error'
                            : ($key === 'due_today' ? 'bg-warning/15 text-warning'
                            : ($key === 'upcoming' ? 'bg-info/15 text-info'
                            : ($key === 'paid' ? 'bg-success/15 text-success'
                            : 'bg-primary/15 text-primary'))))
                          : 'text-base-content/50 hover:text-base-content' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Bills list --}}
    @if ($bills->isEmpty())
        <div class="rounded-xl bg-white/5 border border-base-300 flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <p class="text-sm text-base-content/40 font-medium">Belum ada tagihan</p>
            <p class="text-xs text-base-content/25 mt-1 mb-4">Tambahkan tagihan rutin seperti listrik, internet, dll</p>
            <button onclick="document.getElementById('modal-add').showModal()"
                    class="btn btn-primary btn-sm font-semibold">Tambah Tagihan</button>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($bills as $bill)
                @php
                    $statusConfig = match($bill->computed_status) {
                        'overdue'   => ['color' => 'error',   'label' => 'Terlambat',  'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
                        'due_today' => ['color' => 'warning', 'label' => 'Hari Ini',   'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                        'paid'      => ['color' => 'success', 'label' => 'Lunas',      'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                        default     => ['color' => 'info',    'label' => 'Mendatang',  'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
                    };
                @endphp
                <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden
                            hover:border-{{ $statusConfig['color'] }}/30 transition-colors">

                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-{{ $statusConfig['color'] }}/10 grid place-items-center shrink-0">
                                    <svg class="w-5 h-5 text-{{ $statusConfig['color'] }}" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $statusConfig['icon'] !!}
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-base-content">{{ $bill->title }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="badge badge-xs badge-{{ $statusConfig['color'] }} badge-soft font-mono">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                        @if ($bill->isRecurring())
                                            <span class="text-[10px] text-base-content/25 font-mono">
                                                setiap {{ $bill->repeat_days }} hari
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Dropdown --}}
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-square text-base-content/25 hover:text-base-content">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </div>
                                <ul tabindex="0" class="dropdown-content menu menu-sm z-50 mt-1 w-36 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
                                    <li>
                                        <button onclick="openEditBill({{ $bill->id }}, '{{ addslashes($bill->title) }}', {{ $bill->amount }}, '{{ $bill->due_date->format('Y-m-d') }}', {{ $bill->repeat_days ?? 'null' }})"
                                                class="rounded-lg text-sm">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('bill-reminders.destroy', $bill) }}"
                                              onsubmit="return confirm('Hapus tagihan ini?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg text-sm text-error w-full text-left flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Amount & due date --}}
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-xs text-base-content/35 font-mono mb-0.5">Nominal</p>
                                <p class="text-xl font-bold font-mono text-base-content">
                                    Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-base-content/35 font-mono mb-0.5">Jatuh Tempo</p>
                                <p class="text-sm font-semibold font-mono text-{{ $statusConfig['color'] }}">
                                    {{ $bill->due_date->locale('id')->isoFormat('D MMM YYYY') }}
                                </p>
                                @if ($bill->computed_status === 'upcoming' && $bill->days_until_due > 0)
                                    <p class="text-[10px] text-base-content/25 font-mono">{{ $bill->days_until_due }} hari lagi</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Pay button --}}
                    @if ($bill->computed_status !== 'paid')
                        <div class="border-t border-base-300 px-5 py-3 flex justify-end">
                            <button onclick="openPayBill({{ $bill->id }}, '{{ addslashes($bill->title) }}', {{ $bill->amount }})"
                                    class="btn btn-success btn-sm font-semibold gap-1.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Bayar
                            </button>
                        </div>
                    @else
                        <div class="border-t border-success/10 px-5 py-3">
                            <p class="text-xs text-success/60 font-mono text-right">
                                Dibayar {{ $bill->last_paid_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══════════ MODAL TAMBAH ══════════ --}}
    <dialog id="modal-add" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button></form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Tagihan</h3>
            <p class="text-sm text-base-content/50 mb-6">Atur pengingat tagihan rutin.</p>
            <form method="POST" action="{{ route('bill-reminders.store') }}" class="flex flex-col gap-4">
                @csrf
                <x-ui.input name="title" label="Nama Tagihan" placeholder="Contoh: Listrik, WiFi, Spotify" :required="true" />
                <x-ui.input name="amount" type="number" label="Nominal (Rp)" placeholder="100000" :required="true" />
                <x-ui.input name="due_date" type="date" label="Jatuh Tempo" :required="true" value="{{ now()->format('Y-m-d') }}" />
                <x-ui.input name="repeat_days" type="number" label="Ulangi Setiap (Hari)" placeholder="0 = sekali bayar, 30 = bulanan" />
                <p class="text-[11px] text-base-content/30 -mt-2">Kosongkan atau 0 untuk tagihan sekali bayar.</p>
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Tagihan</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL EDIT ══════════ --}}
    <dialog id="modal-edit" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button></form>
            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Tagihan</h3>
            <p class="text-sm text-base-content/50 mb-6">Perbarui detail tagihan.</p>
            <form id="form-edit-bill" method="POST" class="flex flex-col gap-4">
                @csrf @method('PUT')
                <x-ui.input name="title" label="Nama Tagihan" :required="true" value="" />
                <x-ui.input name="amount" type="number" label="Nominal (Rp)" :required="true" value="" />
                <x-ui.input name="due_date" type="date" label="Jatuh Tempo" :required="true" value="" />
                <x-ui.input name="repeat_days" type="number" label="Ulangi Setiap (Hari)" value="" />
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Perubahan</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL BAYAR ══════════ --}}
    <dialog id="modal-pay" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button></form>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-base-content">Bayar Tagihan</h3>
                    <p id="pay-label" class="text-sm text-base-content/50"></p>
                </div>
            </div>

            <p class="text-sm text-base-content/60 mb-1">Saldo akun akan dikurangi dan tercatat sebagai transaksi pengeluaran.</p>
            <p id="pay-amount" class="text-xl font-bold font-mono text-base-content mb-6"></p>

            <form id="form-pay" method="POST" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Bayar dari Akun</label>
                    <select name="account_id" required class="ft-select">
                        <option value="" disabled selected>Pilih akun</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-full mt-2 font-semibold">Bayar Sekarang</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    <script>
        function openEditBill(id, title, amount, dueDate, repeatDays) {
            const form = document.getElementById('form-edit-bill')
            form.action = '/bill-reminders/' + id
            form.querySelector('[name="title"]').value = title
            form.querySelector('[name="amount"]').value = amount
            form.querySelector('[name="due_date"]').value = dueDate
            form.querySelector('[name="repeat_days"]').value = repeatDays ?? ''
            document.getElementById('modal-edit').showModal()
        }

        function openPayBill(id, title, amount) {
            document.getElementById('form-pay').action = '/bill-reminders/' + id + '/pay'
            document.getElementById('pay-label').textContent = title
            document.getElementById('pay-amount').textContent = 'Rp ' + Number(amount).toLocaleString('id-ID')
            document.getElementById('modal-pay').showModal()
        }
    </script>

</x-layouts.core>