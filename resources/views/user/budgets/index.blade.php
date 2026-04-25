<x-layouts.core title="Budget">

    @php
        $monthNames = [
            '',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
        $overallPct = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0;
        $overallStatus = $overallPct >= 100 ? 'error' : ($overallPct >= 70 ? 'warning' : 'success');
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Budget</h1>
            <p class="text-sm text-base-content/50 mt-1">Atur batas pengeluaran per kategori.</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Month/year picker --}}
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()" class="ft-select !py-1.5 !px-2.5 !text-xs w-auto">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ $monthNames[$m] }}
                        </option>
                    @endfor
                </select>
                <select name="year" onchange="this.form.submit()" class="ft-select !py-1.5 !px-2.5 !text-xs w-auto">
                    @for ($y = now()->year - 1; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endfor
                </select>
            </form>

            @if ($tierSummary['can_add_budget'] && $availableCategories->isNotEmpty())
                <button onclick="document.getElementById('modal-add').showModal()"
                    class="btn btn-primary btn-sm font-semibold gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span class="hidden sm:inline">Tambah</span>
                </button>
            @elseif (!$tierSummary['can_add_budget'])
                <div class="tooltip tooltip-left"
                    data-tip="Batas budget paket {{ $tierSummary['plan']->name }} tercapai">
                    <button class="btn btn-primary btn-sm font-semibold gap-2 btn-disabled" disabled>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        <span class="hidden sm:inline">Tambah</span>
                    </button>
                </div>
            @endif
        </div>
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

    {{-- Summary card --}}
    <div class="rounded-xl bg-white/5 border border-base-300 p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <p class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold mb-1">
                    {{ $monthNames[$month] }} {{ $year }}
                </p>
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                    </p>
                    <span class="text-sm text-base-content/40 font-mono">
                        / Rp {{ number_format($totalBudget, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-bold font-mono text-{{ $overallStatus }}">{{ $overallPct }}%</span>
                @if (!$tierSummary['plan']->isUnlimited('max_budgets'))
                    <span class="badge badge-sm badge-primary badge-soft font-mono text-[10px] font-semibold">
                        {{ $budgetData->count() }}/{{ $tierSummary['plan']->max_budgets }} ·
                        {{ $tierSummary['plan']->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Overall progress bar --}}
        <div class="w-full h-3 rounded-full bg-base-300 overflow-hidden">
            <div class="h-full rounded-full bg-{{ $overallStatus }} transition-all duration-500"
                style="width: {{ min($overallPct, 100) }}%"></div>
        </div>
    </div>

    {{-- Budget cards --}}
    @if ($budgetData->isEmpty())
        <div
            class="rounded-xl bg-white/5 border border-base-300 flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="8" y1="6" x2="21" y2="6" />
                <line x1="8" y1="12" x2="21" y2="12" />
                <line x1="8" y1="18" x2="21" y2="18" />
                <line x1="3" y1="6" x2="3.01" y2="6" />
                <line x1="3" y1="12" x2="3.01" y2="12" />
                <line x1="3" y1="18" x2="3.01" y2="18" />
            </svg>
            <p class="text-sm text-base-content/40 font-medium">Belum ada budget</p>
            <p class="text-xs text-base-content/25 mt-1 mb-4">Set batas pengeluaran per kategori untuk bulan ini</p>
            @if ($availableCategories->isNotEmpty())
                <button onclick="document.getElementById('modal-add').showModal()"
                    class="btn btn-primary btn-sm font-semibold">Tambah Budget</button>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($budgetData as $b)
                @php
                    $barColor = match ($b->status) {
                        'over' => 'error',
                        'warning' => 'warning',
                        default => 'success',
                    };
                @endphp
                <div
                    class="rounded-xl bg-white/5 border border-base-300 p-5 flex flex-col gap-3
                            hover:border-{{ $barColor }}/30 transition-colors group">

                    {{-- Header --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-{{ $barColor }}/10 grid place-items-center shrink-0">
                                <x-ui.icon :name="$b->category->icon ?? 'ellipsis-horizontal'" class="w-4 h-4 text-{{ $barColor }}" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-base-content">{{ $b->category->name }}</p>
                                <p class="text-[11px] text-base-content/35 font-mono">
                                    Limit Rp {{ number_format($b->limit_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
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
                                class="dropdown-content menu menu-sm z-50 mt-1 w-36 rounded-xl bg-base-300 border border-base-300 shadow-xl p-1.5">
                                <li>
                                    <button
                                        onclick="openEditBudget({{ $b->id }}, '{{ addslashes($b->category->name) }}', {{ $b->limit_amount }})"
                                        class="rounded-lg text-sm">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit Limit
                                    </button>
                                </li>
                                <li>
                                    <button
                                        onclick="openDeleteBudget({{ $b->id }}, '{{ addslashes($b->category->name) }}')"
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

                    {{-- Progress bar --}}
                    <div class="w-full h-2.5 rounded-full bg-base-300 overflow-hidden">
                        <div class="h-full rounded-full bg-{{ $barColor }} transition-all duration-500"
                            style="width: {{ min($b->percentage, 100) }}%"></div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-mono text-base-content/50">
                            Rp {{ number_format($b->spent, 0, ',', '.') }}
                            <span class="text-base-content/25">/ Rp
                                {{ number_format($b->limit_amount, 0, ',', '.') }}</span>
                        </p>
                        <span class="text-xs font-bold font-mono text-{{ $barColor }}">
                            {{ $b->percentage }}%
                        </span>
                    </div>

                    {{-- Remaining / Over --}}
                    @if ($b->remaining >= 0)
                        <p class="text-[11px] text-base-content/30 font-mono">
                            Sisa Rp {{ number_format($b->remaining, 0, ',', '.') }}
                        </p>
                    @else
                        <p class="text-[11px] text-error font-mono font-semibold">
                            Melebihi Rp {{ number_format(abs($b->remaining), 0, ',', '.') }}
                        </p>
                    @endif
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

            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Budget</h3>
            <p class="text-sm text-base-content/50 mb-6">Set batas pengeluaran untuk kategori tertentu.</p>

            <form method="POST" action="{{ route('budgets.store') }}" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}" />
                <input type="hidden" name="year" value="{{ $year }}" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Kategori
                    </label>
                    <select name="category_id" required class="ft-select">
                        <option value="" disabled selected>Pilih kategori pengeluaran</option>
                        @foreach ($availableCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @if ($availableCategories->isEmpty())
                        <p class="text-xs text-base-content/30">Semua kategori sudah memiliki budget bulan ini.</p>
                    @endif
                </div>

                <x-ui.input name="limit_amount" type="number" label="Limit Pengeluaran (Rp)"
                    placeholder="Contoh: 500000" :required="true" />

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Budget</button>
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

            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Budget</h3>
            <p id="edit-budget-label" class="text-sm text-base-content/50 mb-6"></p>

            <form id="form-edit-budget" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <x-ui.input name="limit_amount" type="number" label="Limit Pengeluaran (Rp)" placeholder="0"
                    :required="true" value="" />

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
                    <h3 class="text-lg font-semibold text-base-content">Hapus Budget</h3>
                    <p class="text-sm text-base-content/50">Aksi ini tidak bisa dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-base-content/70 mb-6">
                Yakin ingin menghapus budget <strong id="delete-budget-name" class="text-base-content"></strong>?
            </p>

            <form id="form-delete-budget" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete').close()"
                    class="btn btn-ghost flex-1 font-semibold">Batal</button>
                <button type="submit" class="btn btn-error flex-1 font-semibold">Hapus</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>tutup</button></form>
    </dialog>

    <script>
        function openEditBudget(id, name, limit) {
            document.getElementById('form-edit-budget').action = '/budgets/' + id
            document.getElementById('edit-budget-label').textContent = 'Ubah limit budget untuk ' + name
            document.getElementById('form-edit-budget').querySelector('[name="limit_amount"]').value = limit
            document.getElementById('modal-edit').showModal()
        }

        function openDeleteBudget(id, name) {
            document.getElementById('form-delete-budget').action = '/budgets/' + id
            document.getElementById('delete-budget-name').textContent = name
            document.getElementById('modal-delete').showModal()
        }
    </script>

</x-layouts.core>
