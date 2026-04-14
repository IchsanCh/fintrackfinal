<x-layouts.core title="Dashboard">

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="text-xs font-mono uppercase tracking-[0.2em] text-primary font-semibold mb-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <h1 class="text-2xl font-semibold tracking-tight text-base-content">
            Halo, {{ Str::words($user->name, 1, '') }} 👋
        </h1>
        <p class="text-sm text-base-content/50 mt-1">Berikut ringkasan keuangan kamu hari ini.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        {{-- Total Saldo --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Total Saldo
                </span>
                <div class="w-8 h-8 rounded-lg bg-primary/15 grid place-items-center">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                        <line x1="1" y1="10" x2="23" y2="10" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                    Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">dari {{ $stats['account_count'] }} akun</p>
            </div>
        </div>

        {{-- Pemasukan bulan ini --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Pemasukan
                </span>
                <div class="w-8 h-8 rounded-lg bg-success/15 grid place-items-center">
                    <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5" />
                        <polyline points="5 12 12 5 19 12" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-success">
                    Rp {{ number_format($stats['income_this_month'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">bulan ini</p>
            </div>
        </div>

        {{-- Pengeluaran bulan ini --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Pengeluaran
                </span>
                <div class="w-8 h-8 rounded-lg bg-error/15 grid place-items-center">
                    <svg class="w-4 h-4 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <polyline points="19 12 12 19 5 12" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-error">
                    Rp {{ number_format($stats['expense_this_month'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">bulan ini</p>
            </div>
        </div>

        {{-- Tabungan aktif --}}
        <div class="rounded-xl bg-base-200 border border-base-300 p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-widest text-base-content/40 font-semibold">
                    Tabungan
                </span>
                <div class="w-8 h-8 rounded-lg bg-secondary/15 grid place-items-center">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono tracking-tight text-base-content">
                    {{ $stats['active_saving_goals'] }}
                </p>
                <p class="text-xs text-base-content/40 mt-0.5">goals aktif</p>
            </div>
        </div>

    </div>

    {{-- Transaksi terakhir --}}
    <div class="rounded-xl bg-base-200 border border-base-300 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-base-300">
            <h2 class="text-sm font-semibold text-base-content">Transaksi Terakhir</h2>
            <a href="#" class="text-xs text-primary hover:text-primary/70 font-mono transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($recentTransactions->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-10 h-10 text-base-content/20 mb-3" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                <p class="text-sm text-base-content/40">Belum ada transaksi</p>
                <p class="text-xs text-base-content/25 mt-1">Mulai catat pemasukan atau pengeluaranmu</p>
            </div>
        @else
            <ul class="divide-y divide-base-300">
                @foreach ($recentTransactions as $trx)
                    <li class="flex items-center gap-4 px-5 py-3.5 hover:bg-base-300/30 transition-colors">
                        {{-- Icon type --}}
                        <div
                            class="w-9 h-9 rounded-lg shrink-0 grid place-items-center
                                    {{ $trx->type === 'income' ? 'bg-success/10' : 'bg-error/10' }}">
                            <svg class="w-4 h-4 {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                @if ($trx->type === 'income')
                                    <line x1="12" y1="19" x2="12" y2="5" />
                                    <polyline points="5 12 12 5 19 12" />
                                @else
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <polyline points="19 12 12 19 5 12" />
                                @endif
                            </svg>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-base-content truncate">
                                {{ $trx->note ?: $trx->category->name ?? '-' }}
                            </p>
                            <p class="text-xs text-base-content/40 mt-0.5">
                                {{ $trx->category->name ?? '-' }} · {{ $trx->account->name ?? '-' }}
                            </p>
                        </div>

                        {{-- Nominal + tanggal --}}
                        <div class="text-right shrink-0">
                            <p
                                class="text-sm font-semibold font-mono
                                      {{ $trx->type === 'income' ? 'text-success' : 'text-error' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }}Rp
                                {{ number_format($trx->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-base-content/35 mt-0.5 font-mono">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->isoFormat('D MMM') }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</x-layouts.core>
