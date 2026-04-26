<x-layouts.core title="Edit User — {{ $user->name }}">

    {{-- Back --}}
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Kembali
    </a>

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

    <div class="max-w-xl">

        {{-- User info --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-6 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="avatar avatar-placeholder shrink-0">
                    <div class="bg-primary/20 rounded-full w-14 grid place-items-center">
                        <span class="text-xl font-bold font-mono text-primary">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-semibold text-base-content">{{ $user->name }}</h2>
                    <p class="text-sm text-base-content/40 font-mono">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span
                            class="badge badge-sm font-mono {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }} badge-soft">
                            {{ $user->status }}
                        </span>
                        @php $plan = $user->activeSubscription?->plan; @endphp
                        <span
                            class="badge badge-sm badge-soft font-mono
                            {{ $plan
                                ? match ($plan->tier) {
                                    'sultan' => 'badge-warning',
                                    'premium' => 'badge-primary',
                                    default => 'badge-ghost',
                                }
                                : 'badge-ghost' }}">
                            {{ $plan?->name ?? 'Free' }}
                        </span>
                        <span class="text-[11px] text-base-content/25 font-mono">
                            Bergabung {{ $user->created_at->locale('id')->isoFormat('D MMM YYYY') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit form --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-6">
            <h3 class="text-sm font-semibold text-base-content mb-1">Edit User</h3>
            <p class="text-xs text-base-content/40 mb-6">Hanya untuk keadaan darurat. Privasi data keuangan user tetap
                terjaga.</p>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <x-ui.input name="name" label="Nama" :required="true" value="{{ $user->name }}" />
                <x-ui.input name="email" type="email" label="Email" :required="true"
                    value="{{ $user->email }}" />

                <div class="flex flex-col gap-1.5">
                    <label
                        class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">Status</label>
                    <select name="status" required class="ft-select">
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ $user->status === 'banned' ? 'selected' : '' }}>Banned</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">Simpan Perubahan</button>
            </form>
        </div>

    </div>

</x-layouts.core>
