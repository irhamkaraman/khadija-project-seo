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
        $layoutOgImage   = trim($__env->yieldContent('og_image'))   ?: url('/favicon.ico');
        
        $layoutOgImageSecure = $layoutOgImage;
        if (Str::startsWith($layoutOgImageSecure, 'http://') && !str_contains($layoutOgImageSecure, 'localhost') && !str_contains($layoutOgImageSecure, '127.0.0.1')) {
            $layoutOgImageSecure = 'https://' . Str::after($layoutOgImageSecure, 'http://');
        }

        $layoutTwTitle   = trim($__env->yieldContent('twitter_title'))   ?: $layoutOgTitle;
        $layoutTwDesc    = trim($__env->yieldContent('twitter_description')) ?: $layoutOgDesc;
    @endphp

    {{-- ====== OPEN GRAPH (WhatsApp, Facebook, Instagram, LinkedIn) ====== --}}
    <meta property="og:type"                content="@yield('og_type', 'website')">
    <meta property="og:site_name"           content="{{ config('app.name') }}">
    <meta property="og:title"               content="{{ $layoutOgTitle }}">
    <meta property="og:description"         content="{{ $layoutOgDesc }}">
    <meta property="og:url"                 content="@yield('og_url', url()->current())">
    <meta property="og:image"               content="{{ $layoutOgImageSecure }}">
    <meta property="og:image:secure_url"    content="{{ $layoutOgImageSecure }}">
    <meta property="og:image:type"          content="image/jpeg">
    <meta property="og:image:width"         content="1200">
    <meta property="og:image:height"        content="630">
    <meta property="og:image:alt"           content="{{ $layoutOgTitle }}">
    <meta property="og:locale"              content="id_ID">

    {{-- ====== SCHEMA.ORG MICRODATA (WhatsApp & Google Fallback) ====== --}}
    <meta itemprop="name"                   content="{{ $layoutOgTitle }}">
    <meta itemprop="description"            content="{{ $layoutOgDesc }}">
    <meta itemprop="image"                  content="{{ $layoutOgImageSecure }}">
    <link rel="image_src"                   href="{{ $layoutOgImageSecure }}">

    {{-- ====== TWITTER CARD ====== --}}
    <meta name="twitter:card"               content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title"              content="{{ $layoutTwTitle }}">
    <meta name="twitter:description"        content="{{ $layoutTwDesc }}">
    <meta name="twitter:image"              content="{{ $layoutOgImageSecure }}">
    <meta name="twitter:image:src"          content="{{ $layoutOgImageSecure }}">

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
        .dark .article-prose h1, .dark .article-prose h2, .dark .article-prose h3, .dark .article-prose h4, .dark .article-prose h5, .dark .article-prose h6 {
            font-family: 'Playfair Display', serif;
            color: #f8fafc;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.35;
        }
        .dark .article-prose h1 { font-size: 2rem; }
        .dark .article-prose h2 { font-size: 1.5rem; }
        .dark .article-prose h3 { font-size: 1.25rem; }
        .dark .article-prose h4 { font-size: 1.125rem; }
        .dark .article-prose p { margin-bottom: 1.35rem; color: #cbd5e1; line-height: 1.9; font-size: 1.0625rem; }
        .dark .article-prose a { color: #4ade80; text-decoration: underline; font-weight: 500; }
        .dark .article-prose a:hover { color: #86efac; }
        .dark .article-prose img { border-radius: 0.75rem; width: 100%; height: auto; margin: 1.5rem 0; }
        .dark .article-prose ul, .dark .article-prose ol { padding-left: 1.5rem; color: #cbd5e1; margin-bottom: 1.35rem; }
        .dark .article-prose li { margin-bottom: 0.5rem; line-height: 1.8; }
        .dark .article-prose blockquote {
            border-left: 3px solid #22c55e;
            background: rgba(15, 23, 42, 0.4);
            border-radius: 0 0.5rem 0.5rem 0;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-style: italic;
        }
        .dark .article-prose strong { color: #f1f5f9; font-weight: 600; }

        /* Prose light */
        .article-prose h1, .article-prose h2, .article-prose h3, .article-prose h4, .article-prose h5, .article-prose h6 {
            font-family: 'Playfair Display', serif;
            color: #0f172a;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.35;
        }
        .article-prose h1 { font-size: 2rem; }
        .article-prose h2 { font-size: 1.5rem; }
        .article-prose h3 { font-size: 1.25rem; }
        .article-prose h4 { font-size: 1.125rem; }
        .article-prose p { margin-bottom: 1.35rem; color: #334155; line-height: 1.9; font-size: 1.0625rem; }
        .article-prose a { color: #15803d; text-decoration: underline; font-weight: 500; }
        .article-prose a:hover { color: #166534; }
        .article-prose img { border-radius: 0.75rem; width: 100%; height: auto; margin: 1.5rem 0; }
        .article-prose ul, .article-prose ol { padding-left: 1.5rem; color: #334155; margin-bottom: 1.35rem; }
        .article-prose li { margin-bottom: 0.5rem; line-height: 1.8; }
        .article-prose blockquote {
            border-left: 3px solid #16a34a;
            background: #f8fafc;
            border-radius: 0 0.5rem 0.5rem 0;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            color: #475569;
            font-style: italic;
        }
        .article-prose strong { color: #0f172a; font-weight: 600; }

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
    @php
        $navAllCategories = \App\Models\Category::withCount('posts')->orderBy('posts_count', 'desc')->get();
        $navPrimaryCats   = $navAllCategories->take(4);
        $navMoreCats      = $navAllCategories->slice(4);
        $currentCatSlug   = request()->segment(3);
        $isMoreCatActive  = $navMoreCats->contains('slug', $currentCatSlug);
    @endphp

    <header
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 navbar-blur"
        x-data="navApp()"
        x-init="initNav()"
        :class="scrolled
            ? 'dark:bg-urban-950/95 bg-white/95 shadow-lg dark:shadow-black/40 shadow-urban-200/60 dark:border-b dark:border-urban-800/60 border-b border-urban-200/80'
            : 'dark:bg-urban-950/80 bg-white/80 border-b border-urban-200/40 dark:border-urban-800/30'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-forest-500/10 dark:bg-forest-500/20 border border-forest-500/20 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5 text-forest-500 dark:text-forest-400" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="10" width="6" height="18" rx="1" fill="currentColor" opacity="0.5"/>
                            <rect x="5" y="6" width="2" height="4" rx="0.5" fill="currentColor" opacity="0.7"/>
                            <rect x="11" y="4" width="8" height="24" rx="1" fill="currentColor" opacity="0.8"/>
                            <rect x="13" y="1" width="2" height="3" rx="0.5" fill="currentColor"/>
                            <rect x="21" y="7" width="6" height="21" rx="1" fill="currentColor" opacity="0.5"/>
                            <path d="M1 28 C6 20 9 24 12 16 C15 8 18 12 20 20 C22 28 26 22 31 28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" opacity="0.9"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-xl tracking-tight dark:text-white text-urban-900 group-hover:text-forest-600 dark:group-hover:text-forest-300 transition-colors">
                        {{ config('app.name') }}
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-1.5">
                    {{-- Beranda --}}
                    <a href="{{ route('home') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('home') ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100' }}">
                        Beranda
                    </a>

                    {{-- Semua Artikel --}}
                    <a href="{{ route('blog.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('blog.index') ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100' }}">
                        Semua Artikel
                    </a>

                    {{-- Primary Categories --}}
                    @foreach($navPrimaryCats as $cat)
                    @php $isCatActive = request()->is('blog/kategori/' . $cat->slug); @endphp
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ $isCatActive ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach

                    {{-- Dropdown Kategori Lainnya --}}
                    @if($navMoreCats->isNotEmpty())
                    <div class="relative" @click.outside="catDropdownOpen = false">
                        <button @click="catDropdownOpen = !catDropdownOpen"
                                type="button"
                                class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150 {{ $isMoreCatActive ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-800/60 hover:bg-urban-100' }}">
                            <span>Kategori</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="catDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Floating Dropdown Card --}}
                        <div x-show="catDropdownOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 top-full mt-2 w-64 bg-white/95 dark:bg-urban-900/95 backdrop-blur-xl border border-urban-200 dark:border-urban-800 rounded-2xl shadow-2xl p-2 z-50">
                            <div class="text-[11px] font-semibold tracking-wider text-urban-400 dark:text-urban-500 uppercase px-3 py-1.5 mb-1">
                                Kategori Lainnya
                            </div>
                            <div class="space-y-0.5 max-h-72 overflow-y-auto">
                                @foreach($navMoreCats as $cat)
                                @php $isSubCatActive = request()->is('blog/kategori/' . $cat->slug); @endphp
                                <a href="{{ route('blog.category', $cat->slug) }}"
                                   @click="catDropdownOpen = false"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ $isSubCatActive ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold' : 'dark:text-urban-300 text-urban-700 dark:hover:bg-urban-800/80 hover:bg-urban-100 dark:hover:text-white hover:text-urban-900' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isSubCatActive ? 'bg-forest-500' : 'bg-urban-400 dark:bg-urban-600' }}"></span>
                                        <span class="truncate">{{ $cat->name }}</span>
                                    </div>
                                    @if($cat->posts_count > 0)
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-urban-100 dark:bg-urban-800 text-urban-500 dark:text-urban-400 font-mono">
                                        {{ $cat->posts_count }}
                                    </span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </nav>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Dark/Light Mode Toggle --}}
                    <button @click="toggleTheme()"
                            id="theme-toggle-btn"
                            class="theme-toggle flex items-center justify-center w-10 h-10 rounded-xl dark:bg-urban-800/70 bg-urban-100/90 border border-urban-200/50 dark:border-urban-700/50 dark:text-urban-300 text-urban-600 dark:hover:text-yellow-300 hover:text-yellow-500 dark:hover:bg-urban-700/70 hover:bg-urban-200 transition-all shadow-sm"
                            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 110 8 4 4 0 010-8z"/>
                        </svg>
                    </button>

                    {{-- Mobile Hamburger --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl dark:bg-urban-800/70 bg-urban-100/90 border border-urban-200/50 dark:border-urban-700/50 dark:text-urban-300 text-urban-600 dark:hover:text-white hover:text-urban-900 dark:hover:bg-urban-700/70 hover:bg-urban-200 transition-all shadow-sm">
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

        {{-- Mobile Drawer Menu --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden dark:bg-urban-950/95 bg-white/95 navbar-blur dark:border-t dark:border-urban-800/60 border-t border-urban-200/80 px-4 py-4 space-y-3 max-h-[80vh] overflow-y-auto shadow-2xl">
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('home') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('home') ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-700 dark:bg-urban-900/60 bg-urban-100/70' }}">
                    <svg class="w-4 h-4 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('blog.index') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('blog.index') ? 'bg-forest-500/15 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/25' : 'dark:text-urban-300 text-urban-700 dark:bg-urban-900/60 bg-urban-100/70' }}">
                    <svg class="w-4 h-4 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m7-7l-7 7 7 7"/>
                    </svg>
                    Semua Artikel
                </a>
            </div>

            <div class="border-t border-urban-200/60 dark:border-urban-800/60 pt-3">
                <span class="block text-[11px] font-semibold tracking-wider text-urban-400 dark:text-urban-500 uppercase mb-2 px-1">
                    Kategori Berita
                </span>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($navAllCategories as $cat)
                    @php $isMobileCatActive = request()->is('blog/kategori/' . $cat->slug); @endphp
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all {{ $isMobileCatActive ? 'bg-forest-500/20 text-forest-600 dark:text-forest-400 font-semibold border border-forest-500/30' : 'dark:text-urban-300 text-urban-700 dark:bg-urban-900/40 bg-urban-100/50 hover:bg-forest-50 dark:hover:bg-forest-900/40' }}">
                        <span class="truncate">{{ $cat->name }}</span>
                        @if($cat->posts_count > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-urban-200/60 dark:bg-urban-800 text-urban-600 dark:text-urban-400 font-mono">
                            {{ $cat->posts_count }}
                        </span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
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
                        <span class="font-display font-bold text-lg dark:text-white text-urban-950">{{ config('app.name') }}</span>
                    </a>
                    <p class="text-sm dark:text-urban-400 text-urban-600 leading-relaxed">
                        Portal berita dan informasi terpercaya. Temukan artikel pilihan seputar gaya hidup, teknologi, dan kabar terkini.
                    </p>
                </div>

                {{-- Kategori --}}
                <div>
                    <h3 class="font-bold dark:text-urban-200 text-urban-900 mb-4 text-xs uppercase tracking-wider">Kategori</h3>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm dark:text-urban-400 text-urban-600 font-medium dark:hover:text-forest-400 hover:text-forest-600 transition-colors">
                                Semua Artikel
                            </a>
                        </li>
                        @foreach(\App\Models\Category::all() as $cat)
                        <li>
                            <a href="{{ route('blog.category', $cat->slug) }}" class="text-sm dark:text-urban-400 text-urban-600 font-medium dark:hover:text-forest-400 hover:text-forest-600 transition-colors">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Info --}}
                <div>
                    <h3 class="font-bold dark:text-urban-200 text-urban-900 mb-4 text-xs uppercase tracking-wider">Info</h3>
                    <p class="text-sm dark:text-urban-400 text-urban-600 leading-relaxed">
                        Konten kami dipilih secara kurasi untuk memastikan kualitas dan relevansi terbaik bagi pembaca.
                    </p>
                </div>
            </div>

            <div class="dark:border-t dark:border-urban-800/40 border-t border-urban-200/60 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs dark:text-urban-500 text-urban-600 text-center font-medium">
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

    {{-- ===================== GLOBAL AD MODAL ===================== --}}
    <div id="global-ad-modal" class="fixed inset-0 z-[99999] bg-black/80 flex items-center justify-center p-4" style="display: none;">
        <div class="relative bg-white p-2 w-full max-w-lg shadow-2xl">
            <button id="close-global-ad" class="absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shadow-lg font-bold border-2 border-white cursor-pointer hover:bg-red-700 transition-colors z-10">X</button>
            <div id="global-ad-content" class="w-full text-center">
                <!-- Ad image will be injected here -->
            </div>
            <div class="text-center mt-2 text-[10px] text-gray-500 font-sans uppercase tracking-widest">Advertisement</div>
        </div>
    </div>

    {{-- AJAX Ads Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalShown = sessionStorage.getItem('global_ad_modal_shown');
            var globalAdModal = document.getElementById('global-ad-modal');
            var closeGlobalAd = document.getElementById('close-global-ad');
            var globalAdContent = document.getElementById('global-ad-content');

            fetch('/ajax/ads?limit=10')
                .then(response => response.json())
                .then(ads => {
                    if (ads.length === 0) return;

                    var placeholders = document.querySelectorAll('.ajax-ad-slot');
                    placeholders.forEach((el, index) => {
                        // Jangan duplikat iklan jika data iklan lebih sedikit dari slot placeholder
                        if (index >= ads.length) {
                            el.innerHTML = '';
                            el.style.display = 'none';
                            return;
                        }
                        var ad = ads[index];
                        el.innerHTML = `
                            <div class="my-6 text-center">
                                <span class="block text-[10px] text-gray-400 dark:text-urban-500 uppercase tracking-widest mb-1.5 font-sans">Advertisement</span>
                                <a href="${ad.go_url}" target="_self" class="inline-block">
                                    <img src="${ad.image_url}" alt="${ad.title}" class="max-w-full h-auto mx-auto object-contain max-h-[280px]">
                                </a>
                            </div>
                        `;
                    });

                    // Modal Popup Afiliasi: tunggu 20 detik setelah halaman dimuat baru muncul
                    setTimeout(function() {
                        var modalShown = sessionStorage.getItem('global_ad_modal_shown');
                        if (!modalShown && globalAdModal && ads.length > 0) {
                            var randomAd = ads[Math.floor(Math.random() * ads.length)];
                            globalAdContent.innerHTML = `
                                <a href="${randomAd.go_url}" id="global-ad-link" class="block cursor-pointer">
                                    <img src="${randomAd.image_url}" alt="${randomAd.title || 'Iklan'}" class="w-full h-auto object-contain max-h-[70vh] rounded-lg">
                                </a>
                            `;
                            
                            globalAdModal.style.display = 'flex';
                            sessionStorage.setItem('global_ad_modal_shown', '1');

                            function navigateToAd(e) {
                                if (e) e.preventDefault();
                                globalAdModal.style.display = 'none';
                                // Diarahkan di tab yang sama (bukan tab baru) agar langsung membuka aplikasi
                                window.location.href = randomAd.go_url;
                            }

                            // Klik tombol X (tutup) -> otomatis buka aplikasi di tab yang sama
                            closeGlobalAd.addEventListener('click', navigateToAd);

                            // Klik link/gambar iklan -> buka aplikasi di tab yang sama
                            var adLink = document.getElementById('global-ad-link');
                            if (adLink) {
                                adLink.addEventListener('click', navigateToAd);
                            }
                        }
                    }, 20000);
                })
                .catch(err => console.error('Error fetching ads:', err));
        });
    </script>

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
                catDropdownOpen: false,
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
