<x-layouts.core title="Paket Langganan">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Paket Langganan</h1>
            <p class="text-sm text-base-content/50 mt-1">Kelola paket dan batasan fitur per tier.</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Paket
        </a>
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

    {{-- Plans grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @foreach ($plans as $plan)
            @php
                $tierColor = match ($plan->tier) {
                    'free' => 'primary',
                    'premium' => 'info',
                    'sultan' => 'warning',
                    default => 'primary',
                };
            @endphp
            <div
                class="rounded-xl bg-white/5 border border-base-300 overflow-hidden
                        {{ !$plan->is_active ? 'opacity-50' : '' }}
                        hover:border-{{ $tierColor }}/30 transition-colors">

                {{-- Header --}}
                <div class="px-5 py-4 border-b border-base-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="badge badge-sm badge-{{ $tierColor }} badge-soft font-mono text-[10px] uppercase">
                                {{ $plan->tier }}
                            </span>
                            @if (!$plan->is_active)
                                <span class="badge badge-xs badge-ghost font-mono">Nonaktif</span>
                            @endif
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
                                <li>
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="rounded-lg text-sm">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                                        @csrf @method('PATCH')
                                        <button
                                            class="rounded-lg text-sm w-full text-left flex items-center gap-2
                                                       {{ $plan->is_active ? 'text-warning' : 'text-success' }}">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                @if ($plan->is_active)
                                                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                                                    <line x1="12" y1="2" x2="12" y2="12" />
                                                @else
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                    <polyline points="22 4 12 14.01 9 11.01" />
                                                @endif
                                            </svg>
                                            {{ $plan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </li>
                                @if ($plan->subscriptions_count === 0)
                                    <li>
                                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                            onsubmit="return confirm('Hapus paket ini?')">
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
                                @endif
                            </ul>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold text-base-content">{{ $plan->name }}</h2>
                    <p class="text-2xl font-bold font-mono text-{{ $tierColor }} mt-1">
                        {{ $plan->formattedPrice() }}
                    </p>
                    <p class="text-[11px] text-base-content/30 font-mono mt-0.5">
                        {{ $plan->duration_days }} hari · {{ $plan->subscriptions_count }} subscriber
                    </p>
                </div>

                {{-- Limits --}}
                <div class="px-5 py-4 space-y-2.5">
                    @php
                        $limits = [
                            ['label' => 'Akun', 'value' => $plan->max_accounts],
                            ['label' => 'Tabungan', 'value' => $plan->max_saving_goals],
                            ['label' => 'Budget', 'value' => $plan->max_budgets],
                            ['label' => 'AI/bulan', 'value' => $plan->ai_rate_limit],
                        ];
                    @endphp
                    @foreach ($limits as $l)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-base-content/40">{{ $l['label'] }}</span>
                            <span
                                class="text-xs font-mono font-semibold {{ is_null($l['value']) ? 'text-success' : 'text-base-content/60' }}">
                                {{ is_null($l['value']) ? 'Unlimited' : $l['value'] }}
                            </span>
                        </div>
                    @endforeach

                    <div class="pt-1 border-t border-base-300/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-base-content/40">Ekspor</span>
                            @if ($plan->can_export)
                                <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-base-content/20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-base-content/40">Prioritas Support</span>
                            @if ($plan->has_priority_support)
                                <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-base-content/20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.core>
