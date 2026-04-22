<x-layouts.core title="Kategori">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Kategori</h1>
            <p class="text-sm text-base-content/50 mt-1">Kelola kategori pemasukan dan pengeluaranmu.</p>
        </div>
        <button onclick="document.getElementById('modal-add').showModal()"
            class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Kategori
        </button>
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

    {{-- Tabs --}}
    <div class="mb-6">

        {{-- Tab buttons --}}
        <div class="flex gap-1 p-1 bg-white/5 rounded-lg w-fit mb-6 border border-base-300">
            <button id="tab-btn-expense" onclick="switchTab('expense')"
                class="px-4 py-2 rounded-md text-sm font-semibold transition-all bg-error/15 text-error">
                Pengeluaran
                <span class="ml-1 font-mono text-xs opacity-60">({{ $expenses->count() }})</span>
            </button>
            <button id="tab-btn-income" onclick="switchTab('income')"
                class="px-4 py-2 rounded-md text-sm font-semibold transition-all text-base-content/50 hover:text-base-content">
                Pemasukan
                <span class="ml-1 font-mono text-xs opacity-60">({{ $incomes->count() }})</span>
            </button>
        </div>

        {{-- Expense grid --}}
        <div id="tab-expense">
            @if ($expenses->isEmpty())
                <div
                    class="rounded-xl bg-base-200 border border-base-300 flex flex-col items-center justify-center py-16 text-center">
                    <p class="text-sm text-base-content/40">Belum ada kategori pengeluaran</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach ($expenses as $cat)
                        @include('user.categories._card', ['cat' => $cat, 'color' => 'error'])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Income grid --}}
        <div id="tab-income" class="hidden">
            @if ($incomes->isEmpty())
                <div
                    class="rounded-xl bg-base-200 border border-base-300 flex flex-col items-center justify-center py-16 text-center">
                    <p class="text-sm text-base-content/40">Belum ada kategori pemasukan</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach ($incomes as $cat)
                        @include('user.categories._card', ['cat' => $cat, 'color' => 'success'])
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ══════════ MODAL TAMBAH ══════════ --}}
    <dialog id="modal-add" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-200 border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/40">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-base-content mb-1">Tambah Kategori</h3>
            <p class="text-sm text-base-content/50 mb-6">Buat kategori baru untuk transaksimu.</p>

            <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col gap-4">
                @csrf

                <x-ui.input name="name" label="Nama Kategori" placeholder="Contoh: Kopi, Sewa" :required="true" />

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Icon
                    </label>
                    <input type="hidden" name="icon" id="add-icon-value" value="cube" />
                    <div class="grid grid-cols-8 gap-1.5" id="add-icon-grid">
                        @php
                            $iconOptions = [
                                'banknotes',
                                'computer-desktop',
                                'arrow-trending-up',
                                'gift',
                                'wallet',
                                'building-library',
                                'fire',
                                'cake',
                                'truck',
                                'film',
                                'puzzle-piece',
                                'musical-note',
                                'shopping-cart',
                                'shopping-bag',
                                'document-text',
                                'receipt-percent',
                                'bolt',
                                'heart',
                                'academic-cap',
                                'book-open',
                                'home',
                                'wrench',
                                'wifi',
                                'cube',
                                'tag',
                                'ellipsis-horizontal',
                            ];
                        @endphp
                        @foreach ($iconOptions as $ico)
                            <button type="button" onclick="selectIcon('add', '{{ $ico }}')"
                                data-icon="{{ $ico }}" title="{{ $ico }}"
                                class="add-icon-btn w-9 h-9 rounded-lg border grid place-items-center transition-all
                                           {{ $ico === 'cube' ? 'border-primary bg-primary/15 text-primary' : 'border-base-300 bg-base-300/30 text-base-content/50 hover:border-primary/50 hover:text-base-content' }}">
                                <x-ui.icon :name="$ico" class="w-4 h-4" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Tipe
                    </label>
                    <select name="type" required class="ft-select">
                        <option value="expense" selected>Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Kategori</button>
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

            <h3 class="text-lg font-semibold text-base-content mb-1">Edit Kategori</h3>
            <p class="text-sm text-base-content/50 mb-6">Perbarui informasi kategori.</p>

            <form id="form-edit" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <x-ui.input name="name" label="Nama Kategori" placeholder="Nama kategori" :required="true"
                    value="" />
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Icon
                    </label>
                    <input type="hidden" name="icon" id="edit-icon-value" value="cube" />
                    <div class="grid grid-cols-8 gap-1.5" id="edit-icon-grid">
                        @php
                            $iconOptions = [
                                'banknotes',
                                'computer-desktop',
                                'arrow-trending-up',
                                'gift',
                                'wallet',
                                'building-library',
                                'fire',
                                'cake',
                                'truck',
                                'film',
                                'puzzle-piece',
                                'musical-note',
                                'shopping-cart',
                                'shopping-bag',
                                'document-text',
                                'receipt-percent',
                                'bolt',
                                'heart',
                                'academic-cap',
                                'book-open',
                                'home',
                                'wrench',
                                'wifi',
                                'cube',
                                'tag',
                                'ellipsis-horizontal',
                            ];
                        @endphp
                        @foreach ($iconOptions as $ico)
                            <button type="button" onclick="selectIcon('edit', '{{ $ico }}')"
                                data-icon="{{ $ico }}" title="{{ $ico }}"
                                class="edit-icon-btn w-9 h-9 rounded-lg border grid place-items-center transition-all
                                           border-base-300 bg-base-300/30 text-base-content/50 hover:border-primary/50 hover:text-base-content">
                                <x-ui.icon :name="$ico" class="w-4 h-4" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Tipe
                    </label>
                    <select id="edit-type" name="type" required class="ft-select">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

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
                    <h3 class="text-lg font-semibold text-base-content">Hapus Kategori</h3>
                    <p class="text-sm text-base-content/50">Aksi ini tidak bisa dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-base-content/70 mb-6">
                Yakin ingin menghapus kategori <strong id="delete-name" class="text-base-content"></strong>?
            </p>

            <form id="form-delete" method="POST" class="flex gap-3">
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
        function selectIcon(prefix, iconName) {
            document.getElementById(prefix + '-icon-value').value = iconName
            const buttons = document.querySelectorAll('.' + prefix + '-icon-btn')
            buttons.forEach(btn => {
                if (btn.dataset.icon === iconName) {
                    btn.className = btn.className
                        .replace(
                            'border-base-300 bg-base-300/30 text-base-content/50 hover:border-primary/50 hover:text-base-content',
                            '')
                        .replace('border-primary bg-primary/15 text-primary', '')
                    btn.classList.add('border-primary', 'bg-primary/15', 'text-primary')
                } else {
                    btn.className = btn.className
                        .replace('border-primary bg-primary/15 text-primary', '')
                        .replace(
                            'border-base-300 bg-base-300/30 text-base-content/50 hover:border-primary/50 hover:text-base-content',
                            '')
                    btn.classList.add('border-base-300', 'bg-base-300/30', 'text-base-content/50')
                }
            })
        }

        function switchTab(tab) {
            const expensePanel = document.getElementById('tab-expense')
            const incomePanel = document.getElementById('tab-income')
            const expenseBtn = document.getElementById('tab-btn-expense')
            const incomeBtn = document.getElementById('tab-btn-income')

            if (tab === 'expense') {
                expensePanel.classList.remove('hidden')
                incomePanel.classList.add('hidden')
                expenseBtn.className = 'px-4 py-2 rounded-md text-sm font-semibold transition-all bg-error/15 text-error'
                incomeBtn.className =
                    'px-4 py-2 rounded-md text-sm font-semibold transition-all text-base-content/50 hover:text-base-content'
            } else {
                expensePanel.classList.add('hidden')
                incomePanel.classList.remove('hidden')
                incomeBtn.className = 'px-4 py-2 rounded-md text-sm font-semibold transition-all bg-success/15 text-success'
                expenseBtn.className =
                    'px-4 py-2 rounded-md text-sm font-semibold transition-all text-base-content/50 hover:text-base-content'
            }
        }

        function openEditCategory(id, name, icon, type) {
            const form = document.getElementById('form-edit')
            form.action = '/categories/' + id
            form.querySelector('[name="name"]').value = name
            document.getElementById('edit-type').value = type
            selectIcon('edit', icon || 'cube')
            document.getElementById('modal-edit').showModal()
        }

        function openDeleteCategory(id, name) {
            document.getElementById('form-delete').action = '/categories/' + id
            document.getElementById('delete-name').textContent = name
            document.getElementById('modal-delete').showModal()
        }
    </script>

</x-layouts.core>
