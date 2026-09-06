@extends('blog.layout')

@php
    $isSecureScheme = request()->isSecure() || Str::startsWith(config('app.url'), 'https://');
    $scheme         = $isSecureScheme ? 'https://' : 'http://';
    $baseHost       = request()->getHttpHost();

    $seoTitle       = $post->title . ' | ' . config('app.name');
    $seoDesc        = Str::limit(strip_tags($post->content), 155);
    $seoUrl         = $scheme . $baseHost . route('blog.show', $post->slug, false);
    
    if ($post->image_url) {
        $seoImageAbs = $scheme . $baseHost . route('blog.og-image', $post->slug, false);
        
        // Direct image fallback jika crawler butuh alternatif
        if (Str::startsWith($post->image_url, ['http://', 'https://'])) {
            $directImageFallback = $post->image_url;
        } else {
            $directImageFallback = $scheme . $baseHost . '/file/' . ltrim(preg_replace('#^(file/|storage/)#', '', $post->image_url), '/');
        }
    } else {
        $seoImageAbs = $scheme . $baseHost . '/favicon.ico';
        $directImageFallback = null;
    }

    $publishedAt = $post->created_at->toIso8601String();
    $modifiedAt  = $post->updated_at->toIso8601String();
    $catName     = $post->category->name ?? '';
    $catSlug     = $post->category->slug ?? '';

    // JSON-LD dibangun via php array untuk menghindari masalah {} Blade parser
    $jsonLd = json_encode([
        [
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $seoUrl],
            'headline'      => $post->title,
            'description'   => $seoDesc,
            'image'         => [$seoImageAbs, ...($directImageFallback && $directImageFallback !== $seoImageAbs ? [$directImageFallback] : [])],
            'datePublished' => $publishedAt,
            'dateModified'  => $modifiedAt,
            'author'        => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => $scheme . $baseHost],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => config('app.name'),
                'url'   => $scheme . $baseHost,
                'logo'  => ['@type' => 'ImageObject', 'url' => $scheme . $baseHost . '/favicon.ico'],
            ],
            'articleSection' => $catName,
            'inLanguage'     => 'id-ID',
        ],
        [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda',  'item' => $scheme . $baseHost . route('home', [], false)],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $catName,   'item' => $scheme . $baseHost . ($catSlug ? route('blog.category', $catSlug, false) : '')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $seoUrl],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    // Keywords: ambil kata-kata awal konten
    $words = array_slice(preg_split('/\s+/', strip_tags($post->content)), 0, 10);
    $seoKeywords = implode(', ', array_filter($words)) . ', ' . $catName . ', ' . config('app.name');
@endphp

@section('title', $post->title)
@section('seo_title', $seoTitle)
@section('meta_description', $seoDesc)
@section('meta_keywords', $seoKeywords)
@section('meta_author', config('app.name'))
@section('canonical', $seoUrl)
@section('og_type', 'article')
@section('og_title', $post->title)
@section('og_description', $seoDesc)
@section('og_url', $seoUrl)
@section('og_image', $seoImageAbs)
@section('twitter_card', 'summary_large_image')
@section('twitter_title', $post->title)
@section('twitter_description', $seoDesc)

@if(!empty($directImageFallback) && $directImageFallback !== $seoImageAbs)
@section('extra_og_images')
<meta property="og:image" content="{{ $directImageFallback }}">
<meta property="og:image:secure_url" content="{{ $directImageFallback }}">
@endsection
@endif

@section('article_meta')
<meta property="article:published_time" content="{{ $publishedAt }}">
<meta property="article:modified_time"  content="{{ $modifiedAt }}">
<meta property="article:section"        content="{{ $catName }}">
<meta property="article:tag"            content="{{ $catName }}">
@endsection

@section('json_ld')
<script type="application/ld+json">{!! $jsonLd !!}</script>
@endsection

@section('extra_styles')
<style>
    #reading-progress {
        position: fixed; top: 0; left: 0;
        height: 3px;
        background: linear-gradient(90deg, #2d8533, #52a457);
        z-index: 9999;
        transition: width 0.1s linear;
        width: 0%;
    }
    .safelink-overlay {
        position: fixed; top: 0; left: 0;
        width: 100vw; height: 100vh;
        z-index: 999999;
        cursor: default;
    }
</style>
@endsection

@section('content')

<div id="reading-progress"></div>

<article class="px-4 sm:px-6 lg:px-8 pb-20 pt-8">
    <div class="max-w-3xl mx-auto">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm dark:text-urban-400 text-urban-600 mb-8">
            <a href="{{ route('home') }}" class="hover:text-forest-600 dark:hover:text-forest-400 transition-colors">Beranda</a>
            <svg class="w-3.5 h-3.5 dark:text-urban-600 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @if($catSlug)
            <a href="{{ route('blog.category', $catSlug) }}" class="hover:text-forest-600 dark:hover:text-forest-400 transition-colors">{{ $catName }}</a>
            <svg class="w-3.5 h-3.5 dark:text-urban-600 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @endif
            <span class="dark:text-urban-300 text-urban-800 font-medium truncate max-w-xs">{{ Str::limit($post->title, 40) }}</span>
        </nav>

        {{-- Category Badge --}}
        @if($catSlug)
        <div class="mb-5">
            <a href="{{ route('blog.category', $catSlug) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full dark:bg-forest-950/60 bg-forest-100/90 dark:border-forest-700/40 border-forest-300/80 dark:text-forest-400 text-forest-800 text-xs font-semibold uppercase tracking-wider hover:bg-forest-200 dark:hover:bg-forest-900/60 transition-colors shadow-sm">
                <svg class="w-3 h-3 text-forest-600 dark:text-forest-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                {{ $catName }}
            </a>
        </div>
        @endif

        {{-- Title --}}
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black dark:text-white text-urban-950 leading-tight mb-6 tracking-tight">
            {{ $post->title }}
        </h1>

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-4 text-sm dark:text-urban-400 text-urban-600 pb-6 mb-8 border-b dark:border-urban-800/50 border-urban-200">
            <div class="flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4 dark:text-urban-500 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $post->created_at->translatedFormat('d F Y') }}
            </div>
            <div class="flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4 dark:text-urban-500 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ max(1, round(str_word_count(strip_tags($post->content)) / 200)) }} menit baca
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->image_url)
        <div class="relative mb-10 rounded-2xl overflow-hidden shadow-xl dark:shadow-black/50 shadow-urban-200/80 border dark:border-urban-800/40 border-urban-200/80">
            <img src="{{ url('/file/' . $post->image_url) }}" alt="{{ $post->title }}"
                 class="w-full max-h-[500px] object-cover">
            <div class="absolute inset-0 bg-gradient-to-t dark:from-urban-950/60 from-black/20 to-transparent pointer-events-none"></div>
        </div>
        @endif

        {{-- Placeholder Iklan Klasik AJAX (Atas) --}}
        <div class="ajax-ad-slot"></div>

        {{-- Content --}}
        <div class="article-prose">
            {!! $post->content !!}
        </div>

        {{-- Placeholder Iklan Klasik AJAX (Bawah) --}}
        <div class="ajax-ad-slot"></div>

        {{-- Back Button --}}
        @if($catSlug)
        <div class="mt-16 pt-8 border-t dark:border-urban-800/50 border-urban-200">
            <a href="{{ route('blog.category', $catSlug) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl dark:bg-urban-800/60 bg-white border dark:border-urban-700/40 border-urban-200 dark:text-urban-300 text-urban-700 dark:hover:bg-forest-900/60 hover:bg-forest-50 dark:hover:border-forest-700/40 hover:border-forest-300 dark:hover:text-forest-300 hover:text-forest-700 shadow-sm transition-all duration-200 font-medium text-sm">
                <svg class="w-4 h-4 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Artikel {{ $catName }} Lainnya
            </a>
        </div>
        @endif

    </div>
</article>

{{-- Safelink Overlay --}}
@if($randomShareLink)
<div class="safelink-overlay" id="safelinkOverlay" data-href="{{ $randomShareLink }}"></div>
@endif

@endsection

@section('scripts')
<script>
    // Reading Progress Bar
    (function() {
        var bar = document.getElementById('reading-progress');
        if (!bar) return;
        window.addEventListener('scroll', function() {
            var d = document.documentElement;
            var scrolled = d.scrollTop || document.body.scrollTop;
            var total = d.scrollHeight - d.clientHeight;
            bar.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
        });
    })();

    // Safelink Trap — mobile-aware affiliate deep link
    @if($randomShareLink)
    (function() {
        var overlay = document.getElementById('safelinkOverlay');
        var lastOpened = sessionStorage.getItem('affiliate_auto_opened_time');
        var now = Date.now();
        
        // Jika sudah dibuka dalam 10 detik terakhir, hapus overlay (jangan aktif dulu)
        if (lastOpened && (now - parseInt(lastOpened)) < 10000) {
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
            // Pasang timer untuk mengaktifkan kembali jika user masih stay di halaman
            setTimeout(function() {
                if (overlay && !document.getElementById('safelinkOverlay')) {
                    document.body.appendChild(overlay);
                    triggered = false;
                }
            }, 10000 - (now - parseInt(lastOpened)));
        }

        if (overlay) {
            var triggered = false;
            var affiliateUrl = overlay.getAttribute('data-href');

            // Deteksi mobile berdasarkan touch support & user agent
            var isMobile = ('ontouchstart' in window || navigator.maxTouchPoints > 0) &&
                           /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

            function openAffiliate() {
                if (triggered) return;
                triggered = true;
                
                sessionStorage.setItem('affiliate_auto_opened_time', Date.now().toString());

                // BUKAN di tab baru: selalu di tab yang sama agar langsung membuka aplikasi Shopee / TikTok
                window.location.href = affiliateUrl;

                // Hapus overlay sementara, lalu aktifkan kembali setelah 10 detik
                setTimeout(function() {
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                    
                    setTimeout(function() {
                        triggered = false;
                        if (!document.getElementById('safelinkOverlay')) {
                            document.body.appendChild(overlay);
                        }
                    }, 10000);
                }, 300);
            }

        // Desktop: event click biasa
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            openAffiliate();
        });

        // Mobile — touchstart + preventDefault agar scroll tidak membatalkan tap
        var touchMoved = false;
        overlay.addEventListener('touchstart', function(e) {
            touchMoved = false;
        }, { passive: true });

        overlay.addEventListener('touchmove', function(e) {
            touchMoved = true; // tandai jika user sedang scroll, bukan tap
        }, { passive: true });

        overlay.addEventListener('touchend', function(e) {
            if (touchMoved) return; // abaikan jika ini gesture scroll
            e.preventDefault();    // cegah ghost click 300ms
            openAffiliate();
        }, { passive: false });
        }
    })();
    @endif
</script>
@endsection
