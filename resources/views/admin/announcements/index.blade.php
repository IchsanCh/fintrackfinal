<x-layouts.core title="Manajemen Pengumuman">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-base-content">Pengumuman</h1>
            <p class="text-sm text-base-content/50 mt-1">Kelola pengumuman untuk semua pengguna.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm font-semibold gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Buat Pengumuman
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-6"><x-ui.alert type="success">{{ session('success') }}</x-ui.alert></div>
    @endif

    {{-- List --}}
    <div class="rounded-xl bg-white/5 border border-base-300 overflow-hidden">
        @if ($announcements->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg class="w-12 h-12 text-base-content/10 mb-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0" />
                </svg>
                <p class="text-sm text-base-content/40 font-medium">Belum ada pengumuman</p>
                <p class="text-xs text-base-content/25 mt-1 mb-4">Buat pengumuman pertama untuk pengguna</p>
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm font-semibold">
                    Buat Pengumuman
                </a>
            </div>
        @else
            <ul class="divide-y divide-base-300/50">
                @foreach ($announcements as $ann)
                    <li class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div
                                class="w-10 h-10 rounded-lg {{ $ann->is_active ? 'bg-info/10' : 'bg-base-300' }} grid place-items-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 {{ $ann->is_active ? 'text-info' : 'text-base-content/25' }}"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0" />
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-semibold text-base-content truncate">{{ $ann->title }}</p>
                                    @if ($ann->is_active)
                                        <span class="badge badge-xs badge-info badge-soft font-mono">Aktif</span>
                                    @else
                                        <span class="badge badge-xs badge-ghost font-mono">Nonaktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-base-content/40 line-clamp-1">
                                    {{ Str::limit(strip_tags($ann->content), 100) }}
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[11px] text-base-content/25 font-mono">
                                        {{ $ann->admin->name ?? 'Admin' }}
                                    </span>
                                    <span class="text-[11px] text-base-content/15">·</span>
                                    <span class="text-[11px] text-base-content/25 font-mono">
                                        {{ $ann->created_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}
                                    </span>
                                    <span class="text-[11px] text-base-content/15">·</span>
                                    <span class="text-[11px] text-base-content/25 font-mono">
                                        {{ $ann->reads()->count() }} dibaca
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="dropdown dropdown-end shrink-0">
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
                                        <a href="{{ route('admin.announcements.edit', $ann) }}"
                                            class="rounded-lg text-sm">
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
                                        <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}">
                                            @csrf @method('PATCH')
                                            <button
                                                class="rounded-lg text-sm w-full text-left flex items-center gap-2
                                                           {{ $ann->is_active ? 'text-warning' : 'text-success' }}">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    @if ($ann->is_active)
                                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                                                        <line x1="12" y1="2" x2="12"
                                                            y2="12" />
                                                    @else
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                        <polyline points="22 4 12 14.01 9 11.01" />
                                                    @endif
                                                </svg>
                                                {{ $ann->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST"
                                            action="{{ route('admin.announcements.destroy', $ann) }}"
                                            onsubmit="return confirm('Hapus pengumuman ini?')">
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
                                </ul>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($announcements->hasPages())
        <div class="mt-6">{{ $announcements->links() }}</div>
    @endif

</x-layouts.core>
