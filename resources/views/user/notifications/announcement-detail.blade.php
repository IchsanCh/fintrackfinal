<x-layouts.core title="{{ $announcement->title }}">

    <div class="max-w-2xl mx-auto">

        <a href="{{ route('notifications.index', ['tab' => 'announcements']) }}"
            class="inline-flex items-center gap-1.5 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
            Kembali ke pengumuman
        </a>

        <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">

            <div class="px-6 py-5 border-b border-base-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-info/10 grid place-items-center shrink-0">
                        <svg class="w-5 h-5 text-info" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0" />
                        </svg>
                    </div>
                    <div>
                        <span class="badge badge-sm badge-info badge-soft font-mono text-[10px] mb-1">Pengumuman</span>
                        <h1 class="text-lg font-semibold text-base-content">{{ $announcement->title }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-base-content/35 font-mono">
                    <span>{{ $announcement->admin->name ?? 'Admin' }}</span>
                    <span>·</span>
                    <span>{{ $announcement->created_at->locale('id')->isoFormat('D MMMM YYYY · HH:mm') }}</span>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="ft-prose">
                    {!! $announcement->content !!}
                </div>
            </div>

        </div>
    </div>

</x-layouts.core>
