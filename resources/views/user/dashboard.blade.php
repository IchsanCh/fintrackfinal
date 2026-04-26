<x-layouts.core title="Dashboard">

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="text-xs font-mono uppercase tracking-[0.2em] text-primary font-semibold mb-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <h1 class="text-2xl font-semibold tracking-tight text-base-content">
            Halo, {{ Str::words($user->name, 1, '') }} 👋
        </h1>
        <p class="text-sm text-base-content/50 mt-1">Berikut ringkasan keuangan kamu bulan ini.</p>
    </div>

    {{-- ══════════ STAT CARDS ══════════ --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-8">

        {{-- Total Saldo --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 sm:p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] sm:text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">Saldo</span>
                <div class="w-8 h-8 rounded-lg bg-primary/15 grid place-items-center">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                        <line x1="1" y1="10" x2="23" y2="10" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-lg sm:text-2xl font-bold font-mono tracking-tight text-base-content">
                    Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-base-content/35 font-mono mt-0.5">{{ $stats['account_count'] }} akun</p>
            </div>
        </div>

        {{-- Pemasukan --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 sm:p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] sm:text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">Masuk</span>
                <div class="w-8 h-8 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5" />
                        <polyline points="5 12 12 5 19 12" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-lg sm:text-2xl font-bold font-mono tracking-tight text-success">
                    Rp {{ number_format($stats['income_this_month'], 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-base-content/35 font-mono mt-0.5">bulan ini</p>
            </div>
        </div>

        {{-- Pengeluaran --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 sm:p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] sm:text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">Keluar</span>
                <div class="w-8 h-8 rounded-lg bg-error/15 grid place-items-center">
                    <svg class="w-4 h-4 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <polyline points="19 12 12 19 5 12" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-lg sm:text-2xl font-bold font-mono tracking-tight text-error">
                    Rp {{ number_format($stats['expense_this_month'], 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-base-content/35 font-mono mt-0.5">bulan ini</p>
            </div>
        </div>

        {{-- Selisih --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-4 sm:p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] sm:text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">Selisih</span>
                <div
                    class="w-8 h-8 rounded-lg {{ $stats['net_this_month'] >= 0 ? 'bg-success/15' : 'bg-error/15' }} grid place-items-center">
                    <svg class="w-4 h-4 {{ $stats['net_this_month'] >= 0 ? 'text-success' : 'text-error' }}"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>
            <div>
                <p
                    class="text-lg sm:text-2xl font-bold font-mono tracking-tight {{ $stats['net_this_month'] >= 0 ? 'text-success' : 'text-error' }}">
                    {{ $stats['net_this_month'] >= 0 ? '+' : '-' }}Rp
                    {{ number_format(abs($stats['net_this_month']), 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-base-content/35 font-mono mt-0.5">bulan ini</p>
            </div>
        </div>
    </div>

    {{-- ══════════ CHARTS ROW ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

        {{-- Line chart: trend 6 bulan --}}
        <div class="lg:col-span-2 rounded-xl bg-white/5 border border-base-300 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h2 class="text-sm font-semibold text-base-content">Trend Keuangan</h2>
                <div class="flex items-center gap-3">
                    {{-- Legend --}}
                    <span class="flex items-center gap-1.5 text-[11px] text-base-content/40">
                        <span class="w-2.5 h-2.5 rounded-full bg-success"></span> Masuk
                    </span>
                    <span class="flex items-center gap-1.5 text-[11px] text-base-content/40">
                        <span class="w-2.5 h-2.5 rounded-full bg-error"></span> Keluar
                    </span>
                    {{-- Timeframe --}}
                    <div class="flex gap-0.5 p-0.5 bg-white/5 rounded-md border border-base-300">
                        @foreach (['3' => '3B', '6' => '6B', '12' => '1T'] as $val => $label)
                            <a href="{{ route('dashboard', ['trend' => $val]) }}"
                                class="px-2 py-1 rounded text-[10px] font-mono font-semibold transition-all
                                      {{ $trendRange == $val ? 'bg-primary/20 text-primary' : 'text-base-content/30 hover:text-base-content/60' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="relative h-56 sm:h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Doughnut chart: expense per kategori --}}
        <div class="rounded-xl bg-white/5 border border-base-300 p-5">
            <h2 class="text-sm font-semibold text-base-content mb-4">Pengeluaran per Kategori</h2>
            @if ($expenseByCategory->isEmpty())
                <div class="flex flex-col items-center justify-center h-52 text-center">
                    <p class="text-sm text-base-content/30">Belum ada data</p>
                </div>
            @else
                <div class="relative h-44 sm:h-48 mb-4">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach ($expenseByCategory->take(5) as $i => $cat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0"
                                    id="cat-dot-{{ $i }}"></span>
                                <span class="text-xs text-base-content/60 truncate">{{ $cat['name'] }}</span>
                            </div>
                            <span class="text-xs font-mono text-base-content/40">Rp
                                {{ number_format($cat['total'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════ BUDGET + SAVING GOALS ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">

        {{-- Budget progress --}}
        <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-base-300">
                <h2 class="text-sm font-semibold text-base-content">Budget Bulan Ini</h2>
                <a href="{{ route('budgets.index') }}"
                    class="text-xs text-primary hover:text-primary/70 font-mono transition-colors">
                    Lihat semua →
                </a>
            </div>
            @if ($budgets->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm text-base-content/30">Belum ada budget</p>
                </div>
            @else
                <ul class="divide-y divide-base-300/50">
                    @foreach ($budgets as $b)
                        @php
                            $color = $b->percentage >= 100 ? 'error' : ($b->percentage >= 70 ? 'warning' : 'success');
                        @endphp
                        <li class="px-5 py-3.5">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <x-ui.icon :name="$b->category->icon ?? 'ellipsis-horizontal'" class="w-4 h-4 text-{{ $color }}" />
                                    <span
                                        class="text-sm font-medium text-base-content">{{ $b->category->name }}</span>
                                </div>
                                <span
                                    class="text-xs font-bold font-mono text-{{ $color }}">{{ $b->percentage }}%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-base-300 overflow-hidden mb-1">
                                <div class="h-full rounded-full bg-{{ $color }} transition-all duration-500"
                                    style="width: {{ min($b->percentage, 100) }}%"></div>
                            </div>
                            <p class="text-[11px] text-base-content/30 font-mono">
                                Rp {{ number_format($b->spent, 0, ',', '.') }} / Rp
                                {{ number_format($b->limit_amount, 0, ',', '.') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Saving goals --}}
        <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-base-300">
                <h2 class="text-sm font-semibold text-base-content">Target Tabungan</h2>
                <a href="{{ route('saving-goals.index') }}"
                    class="text-xs text-primary hover:text-primary/70 font-mono transition-colors">
                    Lihat semua →
                </a>
            </div>
            @if ($savingGoals->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm text-base-content/30">Belum ada target</p>
                </div>
            @else
                <ul class="divide-y divide-base-300/50">
                    @foreach ($savingGoals as $g)
                        <li class="px-5 py-3.5">
                            <div class="flex items-center justify-between mb-1.5">
                                <span
                                    class="text-sm font-medium text-base-content truncate">{{ $g->title }}</span>
                                <span class="text-xs font-bold font-mono text-primary">{{ $g->percentage }}%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-base-300 overflow-hidden mb-1">
                                <div class="h-full rounded-full bg-primary transition-all duration-500"
                                    style="width: {{ min($g->percentage, 100) }}%"></div>
                            </div>
                            <p class="text-[11px] text-base-content/30 font-mono">
                                Rp {{ number_format($g->current_amount, 0, ',', '.') }} / Rp
                                {{ number_format($g->target_amount, 0, ',', '.') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ══════════ RECENT TRANSACTIONS ══════════ --}}
    <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-base-300">
            <h2 class="text-sm font-semibold text-base-content">Transaksi Terakhir</h2>
            <a href="{{ route('transactions.index') }}"
                class="text-xs text-primary hover:text-primary/70 font-mono transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($recentTransactions->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <p class="text-sm text-base-content/30">Belum ada transaksi</p>
            </div>
        @else
            <ul class="divide-y divide-base-300/50">
                @foreach ($recentTransactions as $trx)
                    <li
                        class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 hover:bg-white/[0.02] transition-colors">
                        <div
                            class="w-9 h-9 rounded-lg shrink-0 grid place-items-center {{ $trx->type === 'income' ? 'bg-success/10' : 'bg-error/10' }}">
                            <x-ui.icon :name="$trx->category->icon ?? 'ellipsis-horizontal'"
                                class="w-4 h-4 {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-base-content truncate">
                                {{ $trx->note ?: $trx->category->name }}</p>
                            <p class="text-[11px] text-base-content/35 mt-0.5 font-mono">{{ $trx->category->name }} ·
                                {{ $trx->account->name }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p
                                class="text-sm font-semibold font-mono {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }}Rp
                                {{ number_format($trx->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-[11px] text-base-content/25 mt-0.5 font-mono">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->isoFormat('D MMM') }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ══════════ CHART.JS ══════════ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Chart = window.Chart

            // ── Shared config ──
            const fontMono = "'Space Mono', monospace"
            const gridColor = 'rgba(255,255,255,0.04)'
            const tickColor = 'rgba(255,255,255,0.25)'

            Chart.defaults.font.family = fontMono
            Chart.defaults.font.size = 11
            Chart.defaults.color = tickColor

            // ── Trend chart (Line) ──
            const trendData = @json($monthlyTrend)

            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.label),
                    datasets: [{
                            label: 'Pemasukan',
                            data: trendData.map(d => d.income),
                            borderColor: 'oklch(72% 0.19 155)',
                            backgroundColor: 'oklch(72% 0.19 155 / 0.1)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'oklch(72% 0.19 155)',
                            pointBorderColor: 'oklch(10% 0.02 280)',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Pengeluaran',
                            data: trendData.map(d => d.expense),
                            borderColor: 'oklch(65% 0.22 17)',
                            backgroundColor: 'oklch(65% 0.22 17 / 0.1)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'oklch(65% 0.22 17)',
                            pointBorderColor: 'oklch(10% 0.02 280)',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'oklch(13% 0.025 278)',
                            borderColor: 'oklch(20% 0.03 280)',
                            borderWidth: 1,
                            titleFont: {
                                family: fontMono,
                                size: 11
                            },
                            bodyFont: {
                                family: fontMono,
                                size: 11
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: tickColor,
                                font: {
                                    size: 10
                                }
                            },
                        },
                        y: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: tickColor,
                                font: {
                                    size: 10
                                },
                                callback: v => v >= 1000000 ? (v / 1000000) + 'jt' : v >= 1000 ? (v /
                                    1000) + 'rb' : v
                            },
                            beginAtZero: true,
                            suggestedMax: Math.max(...trendData.map(d => Math.max(d.income, d.expense)),
                                10000) * 1.15,
                        }
                    }
                }
            })

            // ── Category chart (Doughnut) ──
            const catData = @json($expenseByCategory)

            if (catData.length > 0) {
                const catColors = [
                    'oklch(65% 0.22 17)', // red
                    'oklch(72% 0.19 155)', // green
                    'oklch(56% 0.235 280)', // purple
                    'oklch(70% 0.18 230)', // blue
                    'oklch(78% 0.16 80)', // yellow
                    'oklch(68% 0.2 330)', // pink
                    'oklch(60% 0.15 200)', // teal
                    'oklch(75% 0.12 50)', // orange
                ]

                const catChart = new Chart(document.getElementById('categoryChart'), {
                    type: 'doughnut',
                    data: {
                        labels: catData.map(d => d.name),
                        datasets: [{
                            data: catData.map(d => d.total),
                            backgroundColor: catColors.slice(0, catData.length),
                            borderColor: 'oklch(10% 0.02 280)',
                            borderWidth: 2,
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'oklch(13% 0.025 278)',
                                borderColor: 'oklch(20% 0.03 280)',
                                borderWidth: 1,
                                titleFont: {
                                    family: fontMono,
                                    size: 11
                                },
                                bodyFont: {
                                    family: fontMono,
                                    size: 11
                                },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: ctx => ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                })

                // Set legend dot colors
                catData.forEach((_, i) => {
                    const dot = document.getElementById('cat-dot-' + i)
                    if (dot) dot.style.backgroundColor = catColors[i] || catColors[0]
                })
            }
        })
    </script>

</x-layouts.core>
