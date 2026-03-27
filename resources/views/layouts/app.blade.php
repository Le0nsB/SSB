<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sunny Streetball' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('media/ssb-clips/SSB_favicon2.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('media/ssb-clips/SSB_favicon2.png') }}?v=1">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('ssb-theme');
            if (savedTheme === 'light') {
                document.documentElement.classList.add('theme-light');
            }
        })();
    </script>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="ssb-shell">
    <header class="ssb-header">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="ssb-brand" aria-label="Sunny Streetball sākumlapa">
                <img
                    id="ssb-logo"
                    src="{{ asset('media/ssb-clips/SSB.png') }}"
                    data-static-src="{{ asset('media/ssb-clips/SSB.png') }}"
                    data-gif-src="{{ asset('media/ssb-clips/SSB.gif') }}"
                    alt="Sunny Streetball logo"
                    class="h-10 w-auto object-contain"
                >
            </a>
            <div class="flex items-center gap-3">
                <nav class="flex gap-2 text-sm">
                    <a href="{{ route('home') }}" class="ssb-nav-link {{ request()->routeIs('home') ? 'ssb-nav-link-active' : '' }}">Sākums</a>
                    <a href="{{ route('competitions.index') }}" class="ssb-nav-link {{ request()->routeIs('competitions.*') ? 'ssb-nav-link-active' : '' }}">Sacensības</a>
                    <a href="{{ route('news.index') }}" class="ssb-nav-link {{ request()->routeIs('news.*') ? 'ssb-nav-link-active' : '' }}">Aktualitātes</a>
                </nav>
                <button id="theme-toggle" type="button" class="theme-toggle" aria-label="Mainīt stilu" title="Mainīt stilu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <defs>
                            <clipPath id="theme-toggle-circle-clip">
                                <circle cx="12" cy="12" r="10" />
                            </clipPath>
                        </defs>
                        <g clip-path="url(#theme-toggle-circle-clip)" transform="rotate(45 12 12)">
                            <rect x="2" y="2" width="10" height="20" fill="#e0730d" />
                            <rect x="12" y="2" width="10" height="20" fill="#ffffff" />
                        </g>
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </button>
                @auth
                    <span class="hidden sm:inline text-sm ssb-muted">Sveiki, {{ auth()->user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="theme-toggle" aria-label="Profils" title="Profils">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="theme-toggle" aria-label="Log out" title="Log out">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-logout" aria-hidden="true">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3" />
                                <path d="M18 15l3 -3" />
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="theme-toggle" aria-label="Reģistrēties" title="Reģistrēties">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M16 19h6" />
                            <path d="M19 16v6" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="theme-toggle" aria-label="Log in" title="Log in">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-login" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M21 12h-13l3 -3" />
                            <path d="M11 15l-3 -3" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <script>
        (function () {
            const toggle = document.getElementById('theme-toggle');
            const logo = document.getElementById('ssb-logo');

            if (logo) {
                const staticSrc = logo.dataset.staticSrc;
                const gifSrc = logo.dataset.gifSrc;

                const playGif = () => {
                    logo.src = `${gifSrc}?t=${Date.now()}`;
                };

                const showStatic = () => {
                    logo.src = staticSrc;
                };

                logo.addEventListener('mouseenter', playGif);
                logo.addEventListener('mouseleave', showStatic);
                logo.addEventListener('focus', playGif);
                logo.addEventListener('blur', showStatic);
            }

            if (!toggle) {
                return;
            }

            const root = document.documentElement;

            const updateToggleA11y = () => {
                const isLight = root.classList.contains('theme-light');
                const label = isLight ? 'Pārslēgt uz melns oranžs motīvu' : 'Pārslēgt uz balts oranžs motīvu';
                toggle.setAttribute('aria-label', label);
                toggle.setAttribute('title', label);
            };

            updateToggleA11y();

            toggle.addEventListener('click', function () {
                const isLight = root.classList.toggle('theme-light');
                localStorage.setItem('ssb-theme', isLight ? 'light' : 'dark');
                updateToggleA11y();
            });
        })();
    </script>

</body>
</html>
