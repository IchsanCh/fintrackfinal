<x-layouts.app title="Reset Password — FinTrack">
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
                    Buat password<br>baru yang<br>lebih kuat.
                </h2>
                <p class="text-sm text-base-content/70 leading-relaxed max-w-xs">
                    Masukkan password baru kamu di bawah.
                    Pastikan minimal 8 karakter dan mudah kamu ingat.
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
                        Buat password baru
                    </h1>
                    <p class="text-sm text-base-content/50">
                        Untuk akun <span class="text-base-content/70 font-medium">{{ $email }}</span>
                    </p>
                </div>

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
                <form id="reset-form" method="POST" action="{{ route('reset-password') }}" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Password baru --}}
                    <x-ui.input name="password" type="password" label="Password Baru" placeholder="Minimal 8 karakter"
                        autocomplete="new-password" :autofocus="true" :required="true" :hasIcon="true">
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
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </x-slot:icon>
                    </x-ui.input>

                    {{-- Password strength bars --}}
                    <div class="flex gap-1" id="strength-bars">
                        <div class="ft-bar" id="bar-1"></div>
                        <div class="ft-bar" id="bar-2"></div>
                        <div class="ft-bar" id="bar-3"></div>
                        <div class="ft-bar" id="bar-4"></div>
                    </div>
                    <span id="strength-label" class="font-mono text-xs text-base-content font-semibold min-h-4"></span>

                    {{-- Konfirmasi password --}}
                    <x-ui.input name="password_confirmation" type="password" label="Konfirmasi Password"
                        placeholder="Ulangi password baru" autocomplete="new-password" :required="true"
                        :hasIcon="true">
                        <x-slot:icon>
                            <button type="button" id="toggle-confirm"
                                class="text-base-content/40 hover:text-base-content/70 transition-colors"
                                aria-label="Tampilkan Konfirmasi">
                                <svg id="cf-show" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="cf-hide" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </x-slot:icon>
                    </x-ui.input>
                    <p id="confirm-error" class="text-xs text-error hidden items-center gap-1 -mt-1">
                        Password tidak sama.
                    </p>

                    <button type="submit" id="submit-btn" class="btn btn-primary w-full mt-2 font-semibold">
                        <span id="btn-text">Simpan Password Baru</span>
                        <span id="btn-loading" class="hidden items-center gap-2">
                            <span class="loading loading-spinner loading-xs"></span>
                            Menyimpan...
                        </span>
                    </button>
                </form>

            </div>
        </main>
    </div>

    <script>
        const $ = id => document.getElementById(id)

        // Toggle show/hide password
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
        setupToggle('toggle-confirm', 'password_confirmation', 'cf-show', 'cf-hide')

        // Password strength
        const LABELS = ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat']
        const CLASSES = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong']

        function strength(val) {
            let s = 0
            if (val.length >= 8) s++
            if (/[A-Z]/.test(val)) s++
            if (/[0-9]/.test(val)) s++
            if (/[^A-Za-z0-9]/.test(val)) s++
            return s
        }

        $('password')?.addEventListener('input', function() {
            const score = strength(this.value)
            for (let i = 1; i <= 4; i++)
                $(`bar-${i}`).className = 'ft-bar' + (i <= score ? ' ' + CLASSES[score] : '')
            $('strength-label').textContent = this.value.length ? LABELS[score] : ''
            if ($('password_confirmation').value) checkConfirm()
        })

        function checkConfirm() {
            const match = $('password').value === $('password_confirmation').value
            const err = $('confirm-error')
            const inp = $('password_confirmation')
            if (!match && $('password_confirmation').value) {
                err.classList.remove('hidden');
                err.classList.add('flex')
                inp.classList.add('border-error/50')
            } else {
                err.classList.add('hidden');
                err.classList.remove('flex')
                inp.classList.remove('border-error/50')
            }
        }
        $('password_confirmation')?.addEventListener('input', checkConfirm)

        // Submit
        $('reset-form')?.addEventListener('submit', function(e) {
            let ok = true
            if ($('password').value.length < 8) ok = false
            if ($('password').value !== $('password_confirmation').value) {
                checkConfirm();
                ok = false
            }
            if (!ok) {
                e.preventDefault();
                return
            }
            $('btn-text').classList.add('hidden')
            const l = $('btn-loading')
            l.classList.remove('hidden');
            l.classList.add('flex')
            $('submit-btn').disabled = true
        })
    </script>
</x-layouts.app>
