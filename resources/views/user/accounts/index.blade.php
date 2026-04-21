<x-layouts.core title="Akun Keuangan">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Akun Keuangan</h1>
            <p class="text-sm text-base-content/50 mt-1">Kelola semua akun keuanganmu di sini.</p>
        </div>
        @if ($tierSummary['can_add_account'])
            <button onclick="document.getElementById('modal-add').showModal()"
                class="btn btn-primary btn-sm font-semibold gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Akun
            </button>
        @else
            <div class="tooltip tooltip-left" data-tip="Batas akun paket {{ $tierSummary['plan']->name }} tercapai">
                <button class="btn btn-primary btn-sm font-semibold gap-2 btn-disabled" disabled>
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah Akun
                </button>
            </div>
        @endif
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
        </div>
    @endif

    @if ($errors->has('delete'))
        <div class="mb-6">
            <x-ui.alert type="error">{{ $errors->first('delete') }}</x-ui.alert>
        </div>
    @endif

    @if ($errors->has('limit'))
        <div class="mb-6">
            <x-ui.alert type="warning">{{ $errors->first('limit') }}</x-ui.alert>
        </div>
    @endif

    {{-- Total saldo card --}}
    <div class="rounded-xl bg-white/5 border border-base-300 p-5 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-mono uppercase tracking-widest text-base-content/50 font-semibold mb-1">
                    Total Saldo
                </p>
                <p class="text-3xl font-bold font-mono tracking-tight text-base-content">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary/15 grid place-items-center">
                <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                    <line x1="1" y1="10" x2="23" y2="10" />
                </svg>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-2">
            <p class="text-xs text-base-content/50">dari {{ $accounts->count() }} akun terdaftar</p>
            @if (!$tierSummary['plan']->isUnlimited('max_accounts'))
                @if ($accounts->count() > $tierSummary['plan']->max_accounts)
                    <span class="badge badge-sm badge-warning badge-soft font-semibold font-mono text-[10px]">
                        {{ $accounts->count() }}/{{ $tierSummary['plan']->max_accounts }} · Melebihi batas
                        {{ $tierSummary['plan']->name }}
                    </span>
                @elseif ($accounts->count() == $tierSummary['plan']->max_accounts)
                    <span class="badge badge-sm font-semibold badge-error badge-soft font-mono text-[10px]">
                        {{ $accounts->count() }}/{{ $tierSummary['plan']->max_accounts }} · Penuh
                    </span>
                @else
                    <span class="badge badge-sm font-semibold badge-primary badge-soft font-mono text-[10px]">
                        {{ $accounts->count() }}/{{ $tierSummary['plan']->max_accounts }} ·
                        {{ $tierSummary['plan']->name }}
                    </span>
                @endif
            @else
                <span class="badge badge-sm badge-secondary badge-soft font-mono text-[10px]">
                    Unlimited · {{ $tierSummary['plan']->name }}
                </span>
            @endif
        </div>
    </div>

    {{-- Accounts grid --}}
    @if ($accounts->isEmpty())
        <div
            class="rounded-xl bg-base-200 border border-base-300 flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-12 h-12 text-base-content/15 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                <line x1="1" y1="10" x2="23" y2="10" />
            </svg>
            <p class="text-sm text-base-content/40 font-medium">Belum ada akun</p>
            <p class="text-xs text-base-content/25 mt-1 mb-4">Tambahkan akun pertamamu untuk mulai mencatat keuangan</p>
            <button onclick="document.getElementById('modal-add').showModal()"
                class="btn btn-primary btn-sm font-semibold">
                Tambah Akun
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($accounts as $account)
                @php
                    $typeConfig = match ($account->type) {
                        'cash' => [
                            'label' => 'Tunai',
                            'color' => 'success',
                            'icon' =>
                                '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
                        ],
                        'bank' => [
                            'label' => 'Bank',
                            'color' => 'primary',
                            'icon' =>
                                '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
                        ],
                        'e-wallet' => [
                            'label' => 'E-Wallet',
                            'color' => 'secondary',
                            'icon' =>
                                '<rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="14" r="1.5"/>',
                        ],
                    };
                @endphp
                <div
                    class="rounded-xl bg-white/10 border border-base-300 p-5 flex flex-col gap-4
                            hover:border-{{ $typeConfig['color'] }}/30 transition-colors group">

                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-{{ $typeConfig['color'] }}/15 grid place-items-center shrink-0">
                                <svg class="w-5 h-5 text-{{ $typeConfig['color'] }}" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    {!! $typeConfig['icon'] !!}
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-base-content">{{ $account->name }}</p>
                                <span
                                    class="badge badge-sm badge-{{ $typeConfig['color'] }} badge-soft font-mono font-semibold text-[10px] mt-0.5">
                                    {{ $typeConfig['label'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Dropdown actions --}}
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button"
                                class="btn btn-ghost btn-xs btn-square text-base-content/30 hover:text-base-content">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="5" r="1" />
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="12" cy="19" r="1" />
                                </svg>
                            </div>
                            <ul tabindex="0"
                                class="dropdown-content menu menu-sm z-50 mt-1 w-40 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
                                <li>
                                    <button
                                        onclick="openEditModal({{ $account->id }}, '{{ $account->name }}', '{{ $account->type }}', {{ $account->balance }})"
                                        class="rounded-lg text-sm">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit
                                    </button>
                                </li>
                                <li>
                                    <button onclick="openDeleteModal({{ $account->id }}, '{{ $account->name }}')"
                                        class="rounded-lg text-sm text-error">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        </svg>
                                        Hapus
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Saldo --}}
                    <div>
                        <p class="text-xs font-mono uppercase tracking-widest text-base-content/50 font-semibold mb-1">
                            Saldo
                        </p>
                        <p class="text-xl font-bold font-mono tracking-tight text-base-content">
                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══════════ MODAL TAMBAH ══════════ --}}
    <dialog id="modal-add" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Akun</h3>
            <p class="text-sm text-base-content/50 mb-6">Buat akun baru untuk mencatat keuanganmu.</p>

            <form method="POST" action="{{ route('accounts.store') }}" class="flex flex-col gap-4">
                @csrf

                <x-ui.input name="name" label="Nama Akun" placeholder="Contoh: BCA, Gopay, Dompet"
                    :required="true" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Tipe Akun
                    </label>
                    <select name="type" required class="ft-select">
                        <option value="" disabled selected>Pilih tipe akun</option>
                        <option value="cash">Tunai</option>
                        <option value="bank">Bank</option>
                        <option value="e-wallet">E-Wallet</option>
                    </select>
                </div>

                <x-ui.input name="balance" type="number" label="Saldo Awal" placeholder="0" value="0"
                    :required="true" />

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Akun</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL EDIT ══════════ --}}
    <dialog id="modal-edit" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Akun</h3>
            <p class="text-sm text-base-content/50 mb-6">Perbarui informasi akun kamu.</p>

            <form id="form-edit" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <x-ui.input name="name" label="Nama Akun" placeholder="Contoh: BCA" :required="true"
                    value="" errorField="name" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Tipe Akun
                    </label>
                    <select id="edit-type" name="type" required class="ft-select">
                        <option value="cash">Tunai</option>
                        <option value="bank">Bank</option>
                        <option value="e-wallet">E-Wallet</option>
                    </select>
                </div>

                <x-ui.input name="balance" type="number" label="Saldo" placeholder="0" :required="true"
                    value="" errorField="balance" />

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Perubahan</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    {{-- ══════════ MODAL HAPUS ══════════ --}}
    <dialog id="modal-delete" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-error/15 grid place-items-center">
                    <svg class="w-5 h-5 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-base-content">Hapus Akun</h3>
                    <p class="text-sm text-base-content/50">Aksi ini tidak bisa dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-base-content/70 mb-6">
                Yakin ingin menghapus akun <strong id="delete-name" class="text-base-content"></strong>?
                Semua data terkait akun ini akan ikut terhapus.
            </p>

            <form id="form-delete" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete').close()"
                    class="btn btn-ghost flex-1 font-semibold">
                    Batal
                </button>
                <button type="submit" class="btn btn-error flex-1 font-semibold">
                    Hapus
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    <script>
        function openEditModal(id, name, type, balance) {
            const form = document.getElementById('form-edit')
            form.action = '/accounts/' + id

            // Set values
            form.querySelector('[name="name"]').value = name
            document.getElementById('edit-type').value = type
            form.querySelector('[name="balance"]').value = balance

            document.getElementById('modal-edit').showModal()
        }

        function openDeleteModal(id, name) {
            document.getElementById('form-delete').action = '/accounts/' + id
            document.getElementById('delete-name').textContent = name
            document.getElementById('modal-delete').showModal()
        }
    </script>

</x-layouts.core>
