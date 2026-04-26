<x-layouts.core title="{{ $plan ? 'Edit' : 'Tambah' }} Paket">

    {{-- Back --}}
    <a href="{{ route('admin.plans.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Kembali
    </a>

    @if ($errors->any())
        <div class="mb-6"><x-ui.alert type="error">
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
            </x-ui.alert></div>
    @endif

    <div class="max-w-xl">
        <div class="rounded-xl bg-white/5 border border-base-300 p-6">
            <h2 class="text-lg font-semibold text-base-content mb-1">
                {{ $plan ? 'Edit Paket' : 'Tambah Paket Baru' }}
            </h2>
            <p class="text-sm text-base-content/50 mb-6">Atur tier, harga, dan batasan fitur.</p>

            <form method="POST" action="{{ $plan ? route('admin.plans.update', $plan) : route('admin.plans.store') }}"
                class="flex flex-col gap-5">
                @csrf
                @if ($plan)
                    @method('PUT')
                @endif

                {{-- Tier & Name --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label
                            class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Tier</label>
                        <select name="tier" required class="ft-select" {{ $plan ? 'disabled' : '' }}>
                            <option value="" disabled {{ !$plan ? 'selected' : '' }}>Pilih tier</option>
                            @foreach (['free', 'premium', 'sultan'] as $t)
                                <option value="{{ $t }}"
                                    {{ old('tier', $plan?->tier) === $t ? 'selected' : '' }}>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>
                        @if ($plan)
                            <input type="hidden" name="tier" value="{{ $plan->tier }}" />
                        @endif
                    </div>
                    <x-ui.input name="name" label="Nama Paket" placeholder="Contoh: Premium" :required="true"
                        value="{{ old('name', $plan?->name) }}" />
                </div>

                {{-- Price & Duration --}}
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input name="price" type="number" label="Harga (Rp)" placeholder="49000" :required="true"
                        value="{{ old('price', $plan?->price ?? 0) }}" />
                    <x-ui.input name="duration_days" type="number" label="Durasi (Hari)" placeholder="30"
                        :required="true" value="{{ old('duration_days', $plan?->duration_days ?? 30) }}" />
                </div>

                {{-- Limits --}}
                <div class="border-t border-base-300/50 pt-5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-base-content/50 font-mono mb-4">
                        Batasan Fitur <span class="text-base-content/25">(kosongkan = unlimited)</span>
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input name="max_accounts" type="number" label="Maks Akun" placeholder="Unlimited"
                            value="{{ old('max_accounts', $plan?->max_accounts) }}" />
                        <x-ui.input name="max_saving_goals" type="number" label="Maks Tabungan" placeholder="Unlimited"
                            value="{{ old('max_saving_goals', $plan?->max_saving_goals) }}" />
                        <x-ui.input name="max_budgets" type="number" label="Maks Budget" placeholder="Unlimited"
                            value="{{ old('max_budgets', $plan?->max_budgets) }}" />
                        <x-ui.input name="ai_rate_limit" type="number" label="AI / Bulan" placeholder="Unlimited"
                            value="{{ old('ai_rate_limit', $plan?->ai_rate_limit) }}" />
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="border-t border-base-300/50 pt-5 space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="can_export" class="toggle toggle-primary toggle-sm"
                            {{ old('can_export', $plan?->can_export ?? false) ? 'checked' : '' }} />
                        <div>
                            <span class="text-sm font-medium text-base-content">Ekspor Laporan</span>
                            <p class="text-xs text-base-content/35">User bisa ekspor PDF & Excel</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="has_priority_support" class="toggle toggle-primary toggle-sm"
                            {{ old('has_priority_support', $plan?->has_priority_support ?? false) ? 'checked' : '' }} />
                        <div>
                            <span class="text-sm font-medium text-base-content">Prioritas Support</span>
                            <p class="text-xs text-base-content/35">Dukungan prioritas untuk user</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" class="toggle toggle-success toggle-sm"
                            {{ old('is_active', $plan?->is_active ?? true) ? 'checked' : '' }} />
                        <div>
                            <span class="text-sm font-medium text-base-content">Aktifkan Paket</span>
                            <p class="text-xs text-base-content/35">Paket yang aktif tampil di halaman pricing</p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">
                    {{ $plan ? 'Simpan Perubahan' : 'Buat Paket' }}
                </button>
            </form>
        </div>
    </div>

</x-layouts.core>
