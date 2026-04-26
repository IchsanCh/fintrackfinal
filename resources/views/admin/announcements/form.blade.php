<x-layouts.core title="{{ $announcement ? 'Edit' : 'Buat' }} Pengumuman">

    {{-- Back --}}
    <a href="{{ route('admin.announcements.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Kembali
    </a>

    {{-- Flash --}}
    @if ($errors->any())
        <div class="mb-6"><x-ui.alert type="error">
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
            </x-ui.alert></div>
    @endif

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white/5 border border-base-300 p-6">
            <h2 class="text-lg font-semibold text-base-content mb-1">
                {{ $announcement ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
            </h2>
            <p class="text-sm text-base-content/50 mb-6">
                {{ $announcement ? 'Perbarui isi pengumuman.' : 'Tulis pengumuman untuk semua pengguna.' }}
            </p>

            <form method="POST"
                action="{{ $announcement ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}"
                class="flex flex-col gap-5">
                @csrf
                @if ($announcement)
                    @method('PUT')
                @endif

                {{-- Judul --}}
                <x-ui.input name="title" label="Judul Pengumuman" placeholder="Contoh: Pemeliharaan Server"
                    :required="true" value="{{ old('title', $announcement?->title) }}" />

                {{-- Content: Trix Editor --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-widest text-base-content/80 font-mono">
                        Konten
                    </label>
                    <input id="content-input" type="hidden" name="content"
                        value="{{ old('content', $announcement?->content) }}" />
                    <trix-editor input="content-input" class="trix-content ft-trix"></trix-editor>
                </div>

                {{-- Toggle aktif --}}
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_active" class="toggle toggle-primary toggle-sm"
                        {{ old('is_active', $announcement?->is_active ?? true) ? 'checked' : '' }} />
                    <div>
                        <span
                            class="text-sm font-medium text-base-content group-hover:text-base-content/80 transition-colors">
                            Aktifkan pengumuman
                        </span>
                        <p class="text-xs text-base-content/35">Pengumuman aktif akan tampil di halaman notifikasi user.
                        </p>
                    </div>
                </label>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary w-full mt-2 font-semibold">
                    {{ $announcement ? 'Simpan Perubahan' : 'Terbitkan Pengumuman' }}
                </button>
            </form>
        </div>
    </div>
</x-layouts.core>
