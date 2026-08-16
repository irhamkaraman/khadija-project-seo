<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenuOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- ====== PRIMARY SEO ====== --}}
    @php
        // Blade tidak mendukung ekspresi kompleks di dalam @yield default,
        // jadi kita compute dulu di sini.
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

    {{-- ====== OPEN GRAPH (Facebook, WhatsApp, Telegram, LinkedIn) ====== --}}
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

    {{-- ====== ARTICLE SEO (hanya diisi oleh show.blade.php) ====== --}}
    @yield('article_meta')

    {{-- ====== JSON-LD STRUCTURED DATA ====== --}}
    @yield('json_ld')

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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

        /* Scrollbar custom */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0d1017; }
        ::-webkit-scrollbar-thumb { background: #2d8533; border-radius: 3px; }

        /* Prose styling untuk konten artikel */
        .article-prose h1, .article-prose h2, .article-prose h3 {
            font-family: 'Playfair Display', serif;
            color: #f0f7f0;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .article-prose h1 { font-size: 2rem; }
        .article-prose h2 { font-size: 1.5rem; }
        .article-prose h3 { font-size: 1.25rem; }
        .article-prose p { margin-bottom: 1.25rem; color: #b0b9cc; line-height: 1.9; }
        .article-prose a { color: #52a457; text-decoration: underline; }
        .article-prose img { border-radius: 0.75rem; width: 100%; height: auto; margin: 1.5rem 0; }
        .article-prose ul, .article-prose ol { padding-left: 1.5rem; color: #b0b9cc; margin-bottom: 1.25rem; }
        .article-prose li { margin-bottom: 0.5rem; line-height: 1.8; }
        .article-prose blockquote {
            border-left: 3px solid #2d8533;
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            color: #8593b0;
            font-style: italic;
        }
        .article-prose strong { color: #dceede; font-weight: 600; }

        /* Animasi card hover */
        .post-card { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease; }
        .post-card:hover { transform: translateY(-6px); }

        /* Navbar blur effect */
        .navbar-blur { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        /* Category pill */
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

        @yield('extra_styles')
    </style>
</head>

<body class="bg-urban-950 text-urban-100 font-sans antialiased min-h-screen">

    {{-- ===================== NAVBAR ===================== --}}
    <header
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 navbar-blur"
        :class="scrolled ? 'bg-urban-950/90 shadow-lg shadow-black/30 border-b border-urban-800/50' : 'bg-transparent'"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">

                {{-- Logo --}}
                <a href="{{ route('blog.index') }}" class="flex items-center gap-2.5 group">
                    {{-- SVG Icon: Gedung + Pohon --}}
                    <svg class="w-8 h-8 text-forest-400 group-hover:text-forest-300 transition-colors" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="10" width="6" height="18" rx="1" fill="currentColor" opacity="0.5"/>
                        <rect x="5" y="6" width="2" height="4" rx="0.5" fill="currentColor" opacity="0.7"/>
                        <rect x="11" y="4" width="8" height="24" rx="1" fill="currentColor" opacity="0.8"/>
                        <rect x="13" y="1" width="2" height="3" rx="0.5" fill="currentColor"/>
                        <rect x="21" y="7" width="6" height="21" rx="1" fill="currentColor" opacity="0.5"/>
                        <path d="M1 28 C6 20 9 24 12 16 C15 8 18 12 20 20 C22 28 26 22 31 28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" opacity="0.9"/>
                    </svg>
                    <span class="font-display font-bold text-xl tracking-tight text-white group-hover:text-forest-300 transition-colors">
                        {{ config('app.name') }}
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('blog.index') }}"
                       class="category-pill px-4 py-2 rounded-lg text-sm font-medium text-urban-300 hover:text-white hover:bg-urban-800/60 transition-all duration-200">
                        Semua Artikel
                    </a>
                    @foreach(\App\Models\Category::all() as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="category-pill px-4 py-2 rounded-lg text-sm font-medium text-urban-300 hover:text-white hover:bg-forest-800/60 hover:text-forest-300 transition-all duration-200">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </nav>

                {{-- Mobile Hamburger --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-urban-800/60 text-urban-300 hover:text-white hover:bg-urban-700/60 transition-all">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

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
             class="md:hidden bg-urban-950/95 navbar-blur border-t border-urban-800/50 px-4 py-4 space-y-1">
            <a href="{{ route('blog.index') }}"
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-urban-300 hover:bg-urban-800/60 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m7-7l-7 7 7 7"/>
                </svg>
                Semua Artikel
            </a>
            @foreach(\App\Models\Category::all() as $cat)
            <a href="{{ route('blog.category', $cat->slug) }}"
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-urban-300 hover:bg-forest-900/60 hover:text-forest-300 transition-all">
                <svg class="w-4 h-4 text-forest-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l-1.5 4H6l3.5 2.5-1.5 4L12 10l4 2.5-1.5-4L18 6h-4.5L12 2z"/>
                </svg>
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </header>

    {{-- ===================== HERO BACKGROUND (Kota + Hutan) ===================== --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        {{-- Gradien Langit Kota --}}
        <div class="absolute inset-0 bg-gradient-to-b from-urban-900 via-urban-950 to-urban-950"></div>

        {{-- Siluet Gedung SVG --}}
        <svg class="absolute bottom-0 left-0 right-0 w-full opacity-20" viewBox="0 0 1440 350" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 350V200h20v-80h10V80h10v40h10v-60h30v60h10V90h30v30h5v-50h15v20h10v-20h15v50h5V80h20v-30h10V20h10v30h10V20h10v30h20v-10h10V20h20v10h10V0h15v40h5V0h20v40h5V0h15v10h10V0h20v40h20V20h10v-20h20v60h10v-20h10v20h5v-40h15V0h10v40h10V10h10v30h10V0h15v50h10v-50h20v100h20V70h10V30h10v40h20V20h10v30h10V20h30v30h10V10h20v40h5V10h15v50h10V30h20v20h10V10h20v100h10V50h30v60h20V80h10V30h10v50h20V50h10V20h20v40h20V30h10V0h10v30h10V0h20v70h20V30h10V10h10v20h20V10h10v20h20V20h10V10h20v40h20V10h10V0h20v50h10V20h20v30h20V0h10v50h10V0h15v60h5V20h20v40h20V20h20v40h10V30h10V10h20v50h20V20h10v20h20V30h30V0h10v50h10V0h15v60h5V20h20v40h10V10h10v50h20V0h10v50h10V0h15v60h5V10h25v40h10V20h15v40h15V30h20V0h10v50h10V0h10v40h10V0h20v350H0z" fill="#4a90d9" opacity="0.3"/>
            <path d="M0 350V220h30v-60h20v-80h15v40h25v-60h20v40h20v-40h30v60h10v-60h15v80h25v-50h20v50h30v-70h15v70h15v-50h10v50h20v-100h10v-50h10v50h15v-50h5v50h10v100h20v-80h20v80h10v-50h10v50h20v-30h20v30h10v-70h20v70h10v-40h30v40h20v-60h20v60h10v-30h30v30h20v-50h15v50h5v-80h20v80h20v-40h20v40h10v-60h20v60h10v-30h10v30h20v-50h20v50h10v-20h20v20h10v-40h30v40h10v-60h10v60h20v-30h20v30h10v-60h30v60h10v-30h20v30h10v-50h20v50h20v-70h20v70h20v-30h10v-20h10v50h10v-60h20v60h10v-30h30v30h20v-50h10v50h10v-20h10v20h10v-40h20v40h20v350H0z" fill="#1a3a1a" opacity="0.5"/>
            <path d="M0 350V280l40-40 20 20 30-50 25 30 20-20 30 40 30-60 20 40 40-30 20 30 30-40 20 20 40-50 30 40 20-20 30 30 20-40 40 40 30-20 20 10 40-40 30 30 20-20 40 30 20-20 30 20 20-40 40 40 20-20 30 20 40-30 20 20 30-40 20 40 40-20 30 20 20-40 40 40 30-20 20 10v350H0z" fill="#0d2e11" opacity="0.7"/>
        </svg>

        {{-- Glow effect hijau forest di bawah --}}
        <div class="absolute bottom-0 left-0 right-0 h-64 bg-gradient-to-t from-forest-900/20 to-transparent"></div>
    </div>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="relative z-10 pt-20 lg:pt-24 min-h-screen">
        @yield('content')
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="relative z-10 border-t border-urban-800/40 mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-forest-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="10" width="6" height="18" rx="1" fill="currentColor" opacity="0.5"/>
                        <rect x="11" y="4" width="8" height="24" rx="1" fill="currentColor" opacity="0.8"/>
                        <rect x="21" y="7" width="6" height="21" rx="1" fill="currentColor" opacity="0.5"/>
                        <path d="M1 28 C6 20 9 24 12 16 C15 8 18 12 20 20 C22 28 26 22 31 28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.9"/>
                    </svg>
                    <span class="font-display font-bold text-urban-300">{{ config('app.name') }}</span>
                </div>

                <div class="flex items-center gap-6 text-sm text-urban-500">
                    @foreach(\App\Models\Category::all() as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="hover:text-forest-400 transition-colors">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>

                <p class="text-xs text-urban-600 text-center">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>

    @yield('scripts')

</body>
</html>
