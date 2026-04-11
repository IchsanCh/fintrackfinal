@props(['title' => 'FinTrack'])

<!DOCTYPE html>
<html lang="id" data-theme="fintrack">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base-100 text-base-content">

    {{-- DaisyUI Drawer: sidebar kiri + konten kanan --}}
    <div class="drawer lg:drawer-open">

        {{-- Toggle untuk mobile --}}
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        {{-- ══════════ KONTEN UTAMA ══════════ --}}
        <div class="drawer-content flex flex-col min-h-screen">

            {{-- Topbar --}}
            <x-navigation.topbar :title="$title" />

            {{-- Page content --}}
            <main class="flex-1 p-6 lg:p-8">
                {{ $slot }}
            </main>

        </div>

        {{-- ══════════ SIDEBAR ══════════ --}}
        <div class="drawer-side z-40">
            {{-- Overlay backdrop (mobile) --}}
            <label for="main-drawer" aria-label="Tutup sidebar" class="drawer-overlay"></label>

            {{-- Sidebar content berdasarkan role --}}
            @if (auth()->user()?->role === 'admin')
                <x-navigation.sidebar-admin />
            @else
                <x-navigation.sidebar-user />
            @endif
        </div>

    </div>

</body>

</html>
