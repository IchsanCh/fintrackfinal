<x-layouts.app title="Lupa Password — FinTrack">
    <div class="min-h-screen flex bg-base-100">

        {{-- LEFT PANEL --}}
        <aside
            class="hidden lg:flex w-[44%] xl:w-[40%] shrink-0 flex-col justify-between
                       border-r border-base-300 px-14 py-16
                       bg-gradient-to-br from-primary/10 via-base-100 to-base-100
                       relative overflow-hidden">
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-primary/20 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-secondary/20 blur-3xl rounded-full"></div>

            <div>
                <span class="font-mono font-bold text-sm tracking-widest text-base-content uppercase">
                    FinTrack
                </span>
            </div>

            <div>
                <p class="text-xs font-mono uppercase tracking-[0.2em] font-semibold text-primary mb-6">
                    Reset Password
                </p>
                <h2 class="text-4xl font-semibold leading-tight tracking-tight text-base-content mb-5">
                    Tenang, kita<br>bantu kamu<br>masuk lagi.
                </h2>
                <p class="text-sm text-base-content/70 leading-relaxed max-w-xs">
                    Masukkan email akunmu dan kami akan mengirimkan
                    link untuk membuat password baru.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-6 h-px bg-base-300"></div>
                <p class="text-xs text-base-content/55 font-mono tracking-wide">
                    &copy; {{ date('Y') }} All Right Reserved
                </p>
            </div>
        </aside>

        {{-- RIGHT PANEL --}}
        <main class="flex-1 flex items-center justify-center px-6 py-16">
            <div class="w-full max-w-[360px]">

                {{-- Mobile brand --}}
                <div class="lg:hidden mb-10">
                    <span class="font-mono font-bold text-sm tracking-widest text-base-content/50 uppercase">
                        FinTrack
                    </span>
                </div>

                {{-- Heading --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold tracking-tight text-base-content mb-1">
                        Lupa password?
                    </h1>
                    <p class="text-sm text-base-content/50">
                        Ingat password kamu?
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary/70 transition-colors">
                            Masuk
                        </a>
                    </p>
                </div>

                {{-- Flash success --}}
                @if (session('success'))
                    <div class="mb-6">
                        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                    </div>
                @endif

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mb-6">
                        <x-ui.alert type="error">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    </div>
                @endif

                {{-- Form --}}
                <form id="forgot-form" method="POST" action="{{ route('forgot-password') }}"
                    class="flex flex-col gap-4">
                    @csrf

                    <x-ui.input name="email" type="email" label="Email" placeholder="kamu@email.com"
                        autocomplete="email" :autofocus="true" :required="true" />

                    <p class="text-xs text-base-content/40 -mt-1">
                        Kami akan mengirim link reset password ke email ini.
                    </p>

                    <button type="submit" id="submit-btn" class="btn btn-primary w-full mt-2 font-semibold">
                        <span id="btn-text">Kirim Link Reset</span>
                        <span id="btn-loading" class="hidden items-center gap-2">
                            <span class="loading loading-spinner loading-xs"></span>
                            Mengirim...
                        </span>
                    </button>
                </form>

                {{-- Back --}}
                <div class="mt-8 pt-8 border-t border-base-300">
                    <a href="{{ route('login') }}"
                        class="text-sm flex items-center gap-1.5 text-base-content/50 hover:text-base-content transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Kembali ke halaman login
                    </a>
                </div>

            </div>
        </main>
    </div>

    <script>
        const $ = id => document.getElementById(id)

        $('forgot-form')?.addEventListener('submit', function(e) {
            if (!$('email').value) {
                e.preventDefault();
                return
            }
            $('btn-text').classList.add('hidden')
            const l = $('btn-loading')
            l.classList.remove('hidden')
            l.classList.add('flex')
            $('submit-btn').disabled = true
        })
    </script>
</x-layouts.app>
