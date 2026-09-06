<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <script>
        (function() {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark) || !stored) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- ====== PRIMARY SEO ====== --}}
    @php
        $layoutSeoTitle = trim($__env->yieldContent('seo_title'))
                        ?: (trim($__env->yieldContent('title')) ?: 'Beranda') . ' | ' . config('app.name');
    @endphp
    <title>{{ $layoutSeoTitle }}</title>
    <meta name="description" content="@yield('meta_description', config('app.name') . ' — Portal berita dan informasi terkini seputar gaya hidup, teknologi, dan kabar terbaru.')">
    <meta name="keywords" content="@yield('meta_keywords', 'berita, informasi, artikel, terkini, ' . config('app.name'))">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="@yield('meta_author', config('app.name'))">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    @php
        $layoutOgTitle   = trim($__env->yieldContent('og_title'))   ?: (trim($__env->yieldContent('title'))            ?: config('app.name'));
        $layoutOgDesc    = trim($__env->yieldContent('og_description'))  ?: (trim($__env->yieldContent('meta_description')) ?: config('app.name') . ' — Portal berita dan informasi terkini.');
        $layoutOgImage   = trim($__env->yieldContent('og_image'))   ?: config('app.url') . '/favicon.ico';
        $layoutTwTitle   = trim($__env->yieldContent('twitter_title'))   ?: $layoutOgTitle;
        $layoutTwDesc    = trim($__env->yieldContent('twitter_description')) ?: $layoutOgDesc;
    @endphp

    {{-- ====== OPEN GRAPH ====== --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:site_name"   content="{{ config('app.name') }}">
    <meta property="og:title"       content="{{ $layoutOgTitle }}">
    <meta property="og:description" content="{{ $layoutOgDesc }}">
    <meta property="og:url"         content="@yield('og_url', url()->current())">
    <meta property="og:image"       content="{{ $layoutOgImage }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale"      content="id_ID">

    {{-- ====== TWITTER CARD ====== --}}
    <meta name="twitter:card"        content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title"       content="{{ $layoutTwTitle }}">
    <meta name="twitter:description" content="{{ $layoutTwDesc }}">
    <meta name="twitter:image"       content="{{ $layoutOgImage }}">

    @yield('article_meta')
    @yield('json_ld')

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50:  '#f0f7f0',
                            100: '#dceede',
                            200: '#b8deba',
                            300: '#86c489',
                            400: '#52a457',
                            500: '#2d8533',
                            600: '#1e6b24',
                            700: '#17551c',
                            800: '#134318',
                            900: '#0d2e11',
                        },
                        urban: {
                            50:  '#f6f7f9',
                            100: '#eceef2',
                            200: '#d5d9e3',
                            300: '#b0b9cc',
                            400: '#8593b0',
                            500: '#637298',
                            600: '#4e5a7e',
                            700: '#3f4866',
                            800: '#353d54',
                            900: '#1a1f2e',
                            950: '#0d1017',
                        },
                        concrete: '#c8c9ca',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'Georgia', 'serif'],
                    },
                    backgroundImage: {
                        'city-gradient': 'linear-gradient(135deg, #0d1017 0%, #1a1f2e 40%, #0d2e11 100%)',
                        'card-gradient': 'linear-gradient(180deg, transparent 0%, rgba(13,16,23,0.85) 100%)',
                        'city-gradient-light': 'linear-gradient(135deg, #f0f7f0 0%, #eceef2 40%, #dceede 100%)',
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Transisi mode terang/gelap */
        *, *::before, *::after {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.2s ease;
        }

        /* Scrollbar custom — dark */
        .dark ::-webkit-scrollbar { width: 6px; }
        .dark ::-webkit-scrollbar-track { background: #0d1017; }
        .dark ::-webkit-scrollbar-thumb { background: #2d8533; border-radius: 3px; }

        /* Scrollbar custom — light */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #eceef2; }
        ::-webkit-scrollbar-thumb { background: #52a457; border-radius: 3px; }

        /* Prose dark */
        .dark .article-prose h1, .dark .article-prose h2, .dark .article-prose h3 {
            font-family: 'Playfair Display', serif;
            color: #f0f7f0;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .dark .article-prose h1 { font-size: 2rem; }
        .dark .article-prose h2 { font-size: 1.5rem; }
        .dark .article-prose h3 { font-size: 1.25rem; }
        .dark .article-prose p { margin-bottom: 1.25rem; color: #b0b9cc; line-height: 1.9; }
        .dark .article-prose a { color: #52a457; text-decoration: underline; }
        .dark .article-prose img { border-radius: 0.75rem; width: 100%; height: auto; margin: 1.5rem 0; }
        .dark .article-prose ul, .dark .article-prose ol { padding-left: 1.5rem; color: #b0b9cc; margin-bottom: 1.25rem; }
        .dark .article-prose li { margin-bottom: 0.5rem; line-height: 1.8; }
        .dark .article-prose blockquote {
            border-left: 3px solid #2d8533;
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            color: #8593b0;
            font-style: italic;
        }
        .dark .article-prose strong { color: #dceede; font-weight: 600; }

        /* Prose light */
        .article-prose h1, .article-prose h2, .article-prose h3 {
            font-family: 'Playfair Display', serif;
            color: #1a1f2e;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .article-prose h1 { font-size: 2rem; }
        .article-prose h2 { font-size: 1.5rem; }
        .article-prose h3 { font-size: 1.25rem; }
        .article-prose p { margin-bottom: 1.25rem; color: #3f4866; line-height: 1.9; }
        .article-prose a { color: #1e6b24; text-decoration: underline; }
        .article-prose img { border-radius: 0.75rem; width: 100%; height: auto; margin: 1.5rem 0; }
        .article-prose ul, .article-prose ol { padding-left: 1.5rem; color: #3f4866; margin-bottom: 1.25rem; }
        .article-prose li { margin-bottom: 0.5rem; line-height: 1.8; }
        .article-prose blockquote {
            border-left: 3px solid #2d8533;
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            color: #637298;
            font-style: italic;
        }
        .article-prose strong { color: #134318; font-weight: 600; }

        /* Animasi card hover */
        .post-card { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease; }
        .post-card:hover { transform: translateY(-6px); }

        /* Navbar blur effect */
        .navbar-blur { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        /* Category pill shimmer */
        .category-pill {
            position: relative;
            overflow: hidden;
        }
        .category-pill::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            transform: translateX(-100%);
            transition: transform 0.4s;
        }
        .category-pill:hover::before { transform: translateX(100%); }

        /* Theme toggle button */
        .theme-toggle {
            position: relative;
            width: 40px;
            height: 40px;
        }

        /* Floating background dark */
        .dark .bg-scene {
            background: linear-gradient(135deg, #0d1017 0%, #1a1f2e 40%, #0d2e11 100%);
        }
        /* Floating background light */
        .bg-scene {
            background: linear-gradient(135deg, #f0f7f0 0%, #eceef2 60%, #dceede 100%);
        }

        @yield('extra_styles')
    </style>
</head>

<body class="dark:bg-urban-950 dark:text-urban-100 bg-urban-50 text-urban-900 font-sans antialiased min-h-screen">

    {{-- ===================== NAVBAR ===================== --}}
    <header
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 navbar-blur"
        x-data="navApp()"
        x-init="initNav()"
        :class="scrolled
            ? 'dark:bg-urban-950/90 bg-white/90 shadow-lg dark:shadow-black/30 shadow-urban-200/60 dark:border-b dark:border-urban-800/50 border-b border-urban-200/60'
            : 'bg-transparent'"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <svg class="w-8 h-8 text-forest-500 dark:text-forest-400 group-hover:text-forest-400 dark:group-hover:text-forest-300 transition-colors" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="10" width="6" height="18" rx="1" fill="currentColor" opacity="0.5"/>
                        <rect x="5" y="6" width="2" height="4" rx="0.5" fill="currentColor" opacity="0.7"/>
                        <rect x="11" y="4" width="8" height="24" rx="1" fill="currentColor" opacity="0.8"/>
                        <rect x="13" y="1" width="2" height="3" rx="0.5" fill="currentColor"/>
                        <rect x="21" y="7" width="6" height="21" rx="1" fill="currentColor" opacity="0.5"/>
                        <path d="M1 28 C6 20 9 24 12 16 C15 8 18 12 20 20 C22 28 26 22 31 28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" opacity="0.9"/>
                    </svg>
                    <span class="font-display font-bold text-xl tracking-tight dark:text-white text-urban-900 group-hover:text-forest-600 dark:group-hover:text-forest-300 transition-colors">
                        {{ config('app.name') }}
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="category-pill px-4 py-2 rounded-lg text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100 transition-all duration-200">
                        Beranda
                    </a>
                    <a href="{{ route('blog.index') }}"
                       class="category-pill px-4 py-2 rounded-lg text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100 transition-all duration-200">
                        Semua Artikel
                    </a>
                    @foreach(\App\Models\Category::all() as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="category-pill px-4 py-2 rounded-lg text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-forest-700 dark:hover:bg-forest-800/60 hover:bg-forest-50 dark:hover:text-forest-300 transition-all duration-200">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    {{-- Dark/Light Mode Toggle --}}
                    <button @click="toggleTheme()"
                            id="theme-toggle-btn"
                            class="theme-toggle flex items-center justify-center w-10 h-10 rounded-lg dark:bg-urban-800/60 bg-urban-100 dark:text-urban-300 text-urban-600 dark:hover:text-yellow-300 hover:text-yellow-500 dark:hover:bg-urban-700/60 hover:bg-urban-200 transition-all"
                            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                        {{-- Bulan = tampil saat light mode (agar user bisa klik untuk ke dark) --}}
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        {{-- Matahari = tampil saat dark mode (agar user bisa klik untuk ke light) --}}
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 110 8 4 4 0 010-8z"/>
                        </svg>
                    </button>

                    {{-- Mobile Hamburger --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg dark:bg-urban-800/60 bg-urban-100 dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-700/60 hover:bg-urban-200 transition-all">
                        <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden dark:bg-urban-950/95 bg-white/95 navbar-blur dark:border-t dark:border-urban-800/50 border-t border-urban-200/60 px-4 py-4 space-y-1">
            <a href="{{ route('home') }}"
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:bg-urban-800/60 hover:bg-urban-100 dark:hover:text-white hover:text-urban-900 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Beranda
            </a>
            <a href="{{ route('blog.index') }}"
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:bg-urban-800/60 hover:bg-urban-100 dark:hover:text-white hover:text-urban-900 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m7-7l-7 7 7 7"/>
                </svg>
                Semua Artikel
            </a>
            @foreach(\App\Models\Category::all() as $cat)
            <a href="{{ route('blog.category', $cat->slug) }}"
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium dark:text-urban-300 text-urban-600 dark:hover:bg-forest-900/60 hover:bg-forest-50 dark:hover:text-forest-300 hover:text-forest-700 transition-all">
                <svg class="w-4 h-4 text-forest-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l-1.5 4H6l3.5 2.5-1.5 4L12 10l4 2.5-1.5-4L18 6h-4.5L12 2z"/>
                </svg>
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </header>

    {{-- ===================== BACKGROUND SCENE ===================== --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden bg-scene">
        {{-- Dark: Siluet Gedung SVG --}}
        <svg class="absolute bottom-0 left-0 right-0 w-full dark:opacity-20 opacity-10" viewBox="0 0 1440 350" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 350V200h20v-80h10V80h10v40h10v-60h30v60h10V90h30v30h5v-50h15v20h10v-20h15v50h5V80h20v-30h10V20h10v30h10V20h10v30h20v-10h10V20h20v10h10V0h15v40h5V0h20v40h5V0h15v10h10V0h20v40h20V20h10v-20h20v60h10v-20h10v20h5v-40h15V0h10v40h10V10h10v30h10V0h15v50h10v-50h20v100h20V70h10V30h10v40h20V20h10v30h10V20h30v30h10V10h20v40h5V10h15v50h10V30h20v20h10V10h20v100h10V50h30v60h20V80h10V30h10v50h20V50h10V20h20v40h20V30h10V0h10v30h10V0h20v70h20V30h10V10h10v20h20V10h10v20h20V20h10V10h20v40h20V10h10V0h20v50h10V20h20v30h20V0h10v50h10V0h15v60h5V20h20v40h20V20h20v40h10V30h10V10h20v50h20V20h10v20h20V30h30V0h10v50h10V0h15v60h5V20h20v40h10V10h10v50h20V0h10v50h10V0h15v60h5V10h25v40h10V20h15v40h15V30h20V0h10v50h10V0h10v40h10V0h20v350H0z" fill="#4a90d9" opacity="0.3"/>
            <path d="M0 350V220h30v-60h20v-80h15v40h25v-60h20v40h20v-40h30v60h10v-60h15v80h25v-50h20v50h30v-70h15v70h15v-50h10v50h20v-100h10v-50h10v50h15v-50h5v50h10v100h20v-80h20v80h10v-50h10v50h20v-30h20v30h10v-70h20v70h10v-40h30v40h20v-60h20v60h10v-30h30v30h20v-50h15v50h5v-80h20v80h20v-40h20v40h10v-60h20v60h10v-30h10v30h20v-50h20v50h10v-20h20v20h10v-40h30v40h10v-60h10v60h20v-30h20v30h10v-60h30v60h10v-30h20v30h10v-50h20v50h20v-70h20v70h20v-30h10v-20h10v50h10v-60h20v60h10v-30h30v30h20v-50h10v50h10v-20h10v20h10v-40h20v40h20v350H0z" fill="#1a3a1a" opacity="0.5"/>
            <path d="M0 350V280l40-40 20 20 30-50 25 30 20-20 30 40 30-60 20 40 40-30 20 30 30-40 20 20 40-50 30 40 20-20 30 30 20-40 40 40 30-20 20 10 40-40 30 30 20-20 40 30 20-20 30 20 20-40 40 40 20-20 30 20 40-30 20 20 30-40 20 40 40-20 30 20 20-40 40 40 30-20 20 10v350H0z" fill="#0d2e11" opacity="0.7"/>
        </svg>

        {{-- Glow forest di bawah --}}
        <div class="absolute bottom-0 left-0 right-0 h-64 bg-gradient-to-t from-forest-900/20 to-transparent dark:opacity-100 opacity-30"></div>
    </div>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="relative z-20 pt-20 lg:pt-24 min-h-screen">
        @yield('content')
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="relative z-10 dark:border-t dark:border-urban-800/40 border-t border-urban-200/60 mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                {{-- Brand --}}
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                        <svg class="w-7 h-7 text-forest-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="10" width="6" height="18" rx="1" fill="currentColor" opacity="0.5"/>
                            <rect x="11" y="4" width="8" height="24" rx="1" fill="currentColor" opacity="0.8"/>
                            <rect x="21" y="7" width="6" height="21" rx="1" fill="currentColor" opacity="0.5"/>
                            <path d="M1 28 C6 20 9 24 12 16 C15 8 18 12 20 20 C22 28 26 22 31 28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.9"/>
                        </svg>
                        <span class="font-display font-bold dark:text-urban-200 text-urban-700">{{ config('app.name') }}</span>
                    </a>
                    <p class="text-sm dark:text-urban-500 text-urban-500 leading-relaxed">
                        Portal berita dan informasi terpercaya. Temukan artikel pilihan seputar gaya hidup, teknologi, dan kabar terkini.
                    </p>
                </div>

                {{-- Kategori --}}
                <div>
                    <h3 class="font-semibold dark:text-urban-300 text-urban-700 mb-4 text-sm uppercase tracking-wider">Kategori</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm dark:text-urban-500 text-urban-500 dark:hover:text-forest-400 hover:text-forest-600 transition-colors">
                                Semua Artikel
                            </a>
                        </li>
                        @foreach(\App\Models\Category::all() as $cat)
                        <li>
                            <a href="{{ route('blog.category', $cat->slug) }}" class="text-sm dark:text-urban-500 text-urban-500 dark:hover:text-forest-400 hover:text-forest-600 transition-colors">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Info --}}
                <div>
                    <h3 class="font-semibold dark:text-urban-300 text-urban-700 mb-4 text-sm uppercase tracking-wider">Info</h3>
                    <p class="text-sm dark:text-urban-500 text-urban-500 leading-relaxed">
                        Konten kami dipilih secara kurasi untuk memastikan kualitas dan relevansi terbaik bagi pembaca.
                    </p>
                </div>
            </div>

            <div class="dark:border-t dark:border-urban-800/40 border-t border-urban-200/60 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs dark:text-urban-600 text-urban-500 text-center">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
                </p>
                <button x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                        @click="
                            darkMode = !darkMode;
                            if (darkMode) {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', 'light');
                            }
                        "
                        class="flex items-center gap-2 text-xs dark:text-urban-500 text-urban-500 dark:hover:text-urban-300 hover:text-urban-700 transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 110 8 4 4 0 010-8z"/>
                    </svg>
                    <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>
            </div>
        </div>
    </footer>

    @yield('scripts')

    {{-- Alpine component functions --}}
    <script>
        /**
         * navApp() — Alpine data untuk header navbar.
         * Mengelola state: scrolled, mobileMenuOpen, darkMode.
         * toggleTheme() langsung memanipulasi documentElement.classList
         * agar tidak bergantung pada x-bind:class di <html>.
         */
        function navApp() {
            return {
                mobileMenuOpen: false,
                scrolled: false,
                darkMode: document.documentElement.classList.contains('dark'),

                initNav() {
                    // Sinkronkan state darkMode dengan kondisi DOM aktual (hasil FOUC script di <head>)
                    this.darkMode = document.documentElement.classList.contains('dark');

                    // Listener scroll untuk efek navbar
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 20;
                    });

                    // Listener storage agar tab lain yang mengganti tema juga sinkron
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'theme') {
                            this.darkMode = e.newValue === 'dark';
                            if (this.darkMode) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    });
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    // Manipulasi langsung DOM — ini cara yang 100% reliable
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                }
            };
        }
    </script>

</body>
</html>
