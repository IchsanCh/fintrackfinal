<x-layouts.core title="Notifikasi">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Notifikasi</h1>
            <p class="text-sm text-base-content/50 mt-1">Pantau semua pemberitahuan dan pengumuman.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-6"><x-ui.alert type="success">{{ session('success') }}</x-ui.alert></div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6">
        <div class="flex gap-1 p-1 bg-white/5 rounded-lg w-fit border border-base-300 mb-6">
            <a href="{{ route('notifications.index', ['tab' => 'notifications']) }}"
                class="px-4 py-2 rounded-md text-sm font-semibold transition-all
                      {{ $tab === 'notifications' ? 'bg-primary/15 text-primary' : 'text-base-content/50 hover:text-base-content' }}">
                Notifikasi
                @if ($unreadNotifCount > 0)
                    <span class="ml-1 badge badge-xs badge-error font-mono">{{ $unreadNotifCount }}</span>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['tab' => 'announcements']) }}"
                class="px-4 py-2 rounded-md text-sm font-semibold transition-all
                      {{ $tab === 'announcements' ? 'bg-info/15 text-info' : 'text-base-content/50 hover:text-base-content' }}">
                Pengumuman
                @if ($unreadAnnCount > 0)
                    <span class="ml-1 badge badge-xs badge-info font-mono">{{ $unreadAnnCount }}</span>
                @endif
            </a>
        </div>

        {{-- ══════════ TAB NOTIFIKASI ══════════ --}}
        @if ($tab === 'notifications')

            {{-- Filter type --}}
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-base-300">
                    @foreach (['all' => 'Semua', 'budget_warning' => 'Budget', 'savings_goal' => 'Tabungan', 'bill_reminder' => 'Tagihan'] as $key => $label)
                        <a href="{{ route('notifications.index', ['tab' => 'notifications', 'type' => $key]) }}"
                            class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                                  {{ $type === $key
                                      ? ($key === 'budget_warning'
                                          ? 'bg-warning/15 text-warning'
                                          : ($key === 'savings_goal'
                                              ? 'bg-success/15 text-success'
                                              : ($key === 'bill_reminder'
                                                  ? 'bg-error/15 text-error'
                                                  : 'bg-primary/15 text-primary')))
                                      : 'text-base-content/50 hover:text-base-content' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Actions bar --}}
            @if ($notifications->isNotEmpty())
                <div class="flex items-center justify-end gap-2 mb-4">
                    @if ($unreadNotifCount > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-xs text-primary font-semibold gap-1.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Tandai semua dibaca
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.delete-all') }}"
                        onsubmit="return confirm('Hapus semua notifikasi?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-xs text-error/60 font-semibold gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                            Hapus semua
                        </button>
                    </form>
                </div>
            @endif

            <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
                @if ($notifications->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <p class="text-sm text-base-content/40 font-medium">Tidak ada notifikasi</p>
                    </div>
                @else
                    <ul class="divide-y divide-base-300/50">
                        @foreach ($notifications as $notif)
                            @php
                                $iconConfig = match ($notif->type) {
                                    'budget_warning' => [
                                        'color' => 'warning',
                                        'icon' =>
                                            '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
                                    ],
                                    'savings_goal' => [
                                        'color' => 'success',
                                        'icon' =>
                                            '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                                    ],
                                    'bill_reminder' => [
                                        'color' => 'error',
                                        'icon' =>
                                            '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
                                    ],
                                    default => [
                                        'color' => 'primary',
                                        'icon' =>
                                            '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
                                    ],
                                };
                            @endphp
                            <li
                                class="flex items-center gap-3 px-4 sm:px-5 py-3.5 hover:bg-white/[0.02] transition-colors
                                       {{ !$notif->is_read ? 'bg-primary/[0.03]' : '' }}">

                                {{-- Unread dot --}}
                                <div class="w-2 shrink-0">
                                    @if (!$notif->is_read)
                                        <span class="block w-2 h-2 rounded-full bg-primary"></span>
                                    @endif
                                </div>

                                {{-- Icon --}}
                                <div
                                    class="w-9 h-9 rounded-lg bg-{{ $iconConfig['color'] }}/10 grid place-items-center shrink-0">
                                    <svg class="w-4 h-4 text-{{ $iconConfig['color'] }}" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        {!! $iconConfig['icon'] !!}
                                    </svg>
                                </div>

                                {{-- Message — klik untuk mark read & redirect --}}
                                <form action="{{ route('notifications.read', $notif) }}" method="POST"
                                    class="flex-1 min-w-0 cursor-pointer">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-left w-full">
                                        <p
                                            class="text-sm text-base-content {{ !$notif->is_read ? 'font-semibold' : 'font-medium text-base-content/70' }} leading-relaxed">
                                            {{ $notif->message }}
                                        </p>
                                        <p class="text-[11px] text-base-content/30 font-mono mt-0.5">
                                            {{ $notif->created_at->locale('id')->diffForHumans() }}
                                        </p>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('notifications.destroy', $notif) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-ghost btn-xs btn-square text-base-content/20 hover:text-error shrink-0">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($notifications->hasPages())
                <div class="mt-6">{{ $notifications->links() }}</div>
            @endif

            {{-- ══════════ TAB PENGUMUMAN ══════════ --}}
        @else
            <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
                @if ($announcements->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0" />
                        </svg>
                        <p class="text-sm text-base-content/40 font-medium">Belum ada pengumuman</p>
                    </div>
                @else
                    <ul class="divide-y divide-base-300/50">
                        @foreach ($announcements as $ann)
                            @php $isRead = $readIds->contains($ann->id); @endphp
                            <li>
                                <a href="{{ route('announcements.show', $ann) }}"
                                    class="flex items-center gap-3 px-4 sm:px-5 py-4 hover:bg-white/[0.02] transition-colors
                                          {{ !$isRead ? 'bg-info/[0.03]' : '' }}">

                                    {{-- Unread dot --}}
                                    <div class="w-2 shrink-0">
                                        @if (!$isRead)
                                            <span class="block w-2 h-2 rounded-full bg-info"></span>
                                        @endif
                                    </div>

                                    {{-- Icon --}}
                                    <div class="w-9 h-9 rounded-lg bg-info/10 grid place-items-center shrink-0">
                                        <svg class="w-4 h-4 text-info" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0" />
                                        </svg>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm {{ !$isRead ? 'font-semibold text-base-content' : 'font-medium text-base-content/70' }} truncate">
                                            {{ $ann->title }}
                                        </p>
                                        <p class="text-xs text-base-content/30 mt-0.5 line-clamp-1">
                                            {{ Str::limit(strip_tags($ann->content), 80) }}
                                        </p>
                                        <p class="text-[11px] text-base-content/25 font-mono mt-1">
                                            {{ $ann->created_at->locale('id')->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Arrow --}}
                                    <svg class="w-4 h-4 text-base-content/15 shrink-0" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6" />
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($announcements->hasPages())
                <div class="mt-6">{{ $announcements->links() }}</div>
            @endif

        @endif
    </div>

</x-layouts.core>
