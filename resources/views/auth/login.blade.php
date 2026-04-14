<x-layouts.app title="Login">
    <div class="min-h-screen flex bg-base-100">

        {{-- ══════════════════════════════════════
         LEFT PANEL
    ══════════════════════════════════════ --}}
        <aside
            class="hidden lg:flex w-[44%] xl:w-[40%] shrink-0 flex-col justify-between
            border-r border-base-300 px-14 py-16
            bg-gradient-to-br from-primary/10 via-base-100 to-base-100
            relative overflow-hidden">
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-primary/20 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-secondary/20 blur-3xl rounded-full"></div>

            {{-- Brand --}}
            <div>
                <span class="font-mono font-bold text-sm tracking-widest text-base-content uppercase">
                    FinTrack
                </span>
            </div>

            {{-- Center copy --}}
            <div>
                <p class="text-xs font-mono uppercase tracking-[0.2em] font-semibold text-primary mb-6">
                    Personal Finance
                </p>
                <h2 class="text-4xl font-semibold leading-tight tracking-tight text-base-content mb-5">
                    Semua catatan<br>keuangan kamu,<br>dalam satu tempat.
                </h2>
                <p class="text-sm text-base-content/70 leading-relaxed max-w-xs">
                    Catat pemasukan, lacak pengeluaran, dan rencanakan keuanganmu
                    tanpa ribet.
                </p>
            </div>

            {{-- Bottom tagline --}}
            <div class="flex items-center gap-3">
                <div class="w-6 h-px bg-base-300"></div>
                <p class="text-xs text-base-content/55 font-mono tracking-wide">&copy; {{ date('Y') }} All Right
                    Reserved</p>
            </div>
        </aside>

        {{-- ══════════════════════════════════════
         RIGHT PANEL
    ══════════════════════════════════════ --}}
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
                        Selamat datang kembali
                    </h1>
                    <p class="text-sm text-base-content/50">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary hover:text-primary/70 transition-colors">
                            Daftar
                        </a>
                    </p>
                </div>

                {{-- Flash success (dari verify OTP) --}}
                @if (session('success'))
                    <div class="mb-6">
                        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                    </div>
                @endif

                {{-- Server errors --}}
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
                <form id="login-form" method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                    @csrf

                    {{-- Email --}}
                    <x-ui.input name="email" type="email" label="Email" placeholder="kamu@email.com"
                        autocomplete="email" :autofocus="true" :required="true" />

                    {{-- Password --}}
                    <div class="flex flex-col gap-1.5">
                        <x-ui.input name="password" type="password" label="Password" placeholder="Password kamu"
                            autocomplete="current-password" :required="true" :hasIcon="true">
                            <x-slot:icon>
                                <button type="button" id="toggle-password"
                                    class="text-base-content/40 hover:text-base-content/70 transition-colors"
                                    aria-label="Tampilkan Password">
                                    <svg id="pw-show" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg id="pw-hide" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                        <path
                                            d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                </button>
                            </x-slot:icon>
                        </x-ui.input>

                        {{-- Lupa password --}}
                        <div class="flex justify-end mt-1">
                            <a href="{{ route('forgot-password') }}"
                                class="text-xs text-base-content/60 font-semibold hover:text-primary transition-colors">
                                Lupa password?
                            </a>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="submit-btn" class="btn btn-primary w-full mt-2 font-semibold">
                        <span id="btn-text">Masuk</span>
                        <span id="btn-loading" class="hidden items-center gap-2">
                            <span class="loading loading-spinner loading-xs"></span>
                            Memproses...
                        </span>
                    </button>
                </form>

            </div>
        </main>
    </div>

    <script>
        const $ = id => document.getElementById(id)

        // Toggle password visibility
        function setupToggle(btnId, inputId, showId, hideId) {
            $(btnId)?.addEventListener('click', () => {
                const input = $(inputId)
                const isHidden = input.type === 'password'
                input.type = isHidden ? 'text' : 'password'
                $(showId).classList.toggle('hidden', isHidden)
                $(hideId).classList.toggle('hidden', !isHidden)
            })
        }
        setupToggle('toggle-password', 'password', 'pw-show', 'pw-hide')

        // Loading state on submit
        $('login-form')?.addEventListener('submit', function(e) {
            const email = $('email').value
            const password = $('password').value

            if (!email || !password) {
                e.preventDefault()
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
