@extends('blog.layout')

@php
    $isSecureScheme = request()->isSecure() || Str::startsWith(config('app.url'), 'https://');
    $scheme         = $isSecureScheme ? 'https://' : 'http://';
    $baseHost       = request()->getHttpHost();

    $seoTitle       = $post->title . ' | ' . config('app.name');
    $seoDesc        = Str::limit(strip_tags($post->content), 155);
    $seoUrl         = $scheme . $baseHost . route('blog.show', $post->slug, false);
    
    if ($post->image_url) {
        $seoImageAbs = $post->asset_url;
        $directImageFallback = $post->asset_url;
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
        <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm dark:text-urban-400 text-urban-600 mb-8">
            <a href="{{ route('home') }}" class="hover:text-forest-600 dark:hover:text-forest-400 transition-colors shrink-0 whitespace-nowrap">Beranda</a>
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 dark:text-urban-600 text-urban-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @if($catSlug)
            <a href="{{ route('blog.category', $catSlug) }}" class="hover:text-forest-600 dark:hover:text-forest-400 transition-colors shrink-0 whitespace-nowrap">{{ $catName }}</a>
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 dark:text-urban-600 text-urban-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @endif
            <span class="dark:text-urban-300 text-urban-800 font-medium truncate flex-1 min-w-[100px]">{{ $post->title }}</span>
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

        {{-- Meta & Share --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 pb-6 mb-8 border-b dark:border-urban-800/50 border-urban-200">
            <div class="flex flex-wrap items-center gap-4 text-sm dark:text-urban-400 text-urban-600">
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
            
            @php
                // Generate random ref base64 for bypassing cache on sharing
                $randomRef = rtrim(strtr(base64_encode(random_bytes(6)), '+/', '-_'), '=');
                $shareUrl = $seoUrl . '?ref=' . $randomRef;
                $encodedShareUrl = urlencode($shareUrl);
                $encodedTitle = urlencode($post->title);
            @endphp
            <div class="flex items-center gap-3">
                <span class="text-[11px] font-bold uppercase tracking-wider dark:text-urban-500 text-urban-400 mr-1">Bagikan:</span>
                
                {{-- WhatsApp --}}
                <a href="https://api.whatsapp.com/send?text={{ $encodedTitle }}%20{{ $encodedShareUrl }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-full bg-[#25D366] text-white hover:bg-[#1DA851] transition-transform hover:scale-110 shadow-sm" title="Bagikan ke WhatsApp">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
                
                {{-- Facebook --}}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedShareUrl }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-full bg-[#1877F2] text-white hover:bg-[#1664D9] transition-transform hover:scale-110 shadow-sm" title="Bagikan ke Facebook">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </a>
                
                {{-- X / Twitter --}}
                <a href="https://twitter.com/intent/tweet?url={{ $encodedShareUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-full bg-black text-white hover:bg-gray-800 transition-transform hover:scale-110 shadow-sm" title="Bagikan ke X (Twitter)">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                
                {{-- Salin Link (khususnya untuk TikTok dsb) --}}
                <button type="button" onclick="copyShareLink('{{ $shareUrl }}')" class="p-2.5 rounded-full bg-urban-100 dark:bg-urban-800 text-urban-700 dark:text-urban-300 hover:bg-urban-200 dark:hover:bg-urban-700 transition-transform hover:scale-110 shadow-sm border border-urban-200 dark:border-urban-700" title="Salin Link">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->image_url)
        <div class="relative mb-10 rounded-2xl overflow-hidden shadow-xl dark:shadow-black/50 shadow-urban-200/80 border dark:border-urban-800/40 border-urban-200/80">
            <img src="{{ $post->asset_url }}" alt="{{ $post->title }}"
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

    {{-- REKOMENDASI BACAAN --}}
    <div class="max-w-6xl mx-auto mt-20 pt-12 border-t dark:border-urban-800/60 border-urban-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            
            {{-- Terpopuler --}}
            <div>
                <h3 class="font-display font-bold text-xl mb-6 dark:text-white text-urban-950 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Paling Populer
                </h3>
                <div class="space-y-6">
                    @foreach($popularPosts as $rp)
                    <a href="{{ route('blog.show', $rp->slug) }}" class="group flex gap-4 items-start">
                        <div class="shrink-0 w-24 h-20 rounded-xl overflow-hidden bg-urban-100 dark:bg-urban-800 border dark:border-urban-800/50 border-urban-200">
                            <img src="{{ $rp->asset_url }}" alt="{{ $rp->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm dark:text-urban-200 text-urban-800 group-hover:text-forest-600 dark:group-hover:text-forest-400 transition-colors line-clamp-2 leading-snug">{{ $rp->title }}</h4>
                            <p class="text-[11px] font-medium dark:text-urban-500 text-urban-500 mt-1.5 flex items-center gap-1.5">
                                <span>{{ $rp->created_at->translatedFormat('d M Y') }}</span>
                                <span class="w-1 h-1 rounded-full bg-urban-300 dark:bg-urban-600"></span>
                                <span class="text-red-600 dark:text-red-400 font-semibold">{{ number_format($rp->views) }}x dibaca</span>
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Terkait --}}
            <div>
                <h3 class="font-display font-bold text-xl mb-6 dark:text-white text-urban-950 flex items-center gap-2">
                    <svg class="w-5 h-5 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Artikel Terkait
                </h3>
                <div class="space-y-6">
                    @foreach($relatedPosts as $rp)
                    <a href="{{ route('blog.show', $rp->slug) }}" class="group flex gap-4 items-start">
                        <div class="shrink-0 w-24 h-20 rounded-xl overflow-hidden bg-urban-100 dark:bg-urban-800 border dark:border-urban-800/50 border-urban-200">
                            <img src="{{ $rp->asset_url }}" alt="{{ $rp->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm dark:text-urban-200 text-urban-800 group-hover:text-forest-600 dark:group-hover:text-forest-400 transition-colors line-clamp-2 leading-snug">{{ $rp->title }}</h4>
                            <p class="text-[11px] font-medium dark:text-urban-500 text-urban-500 mt-1.5 flex items-center gap-1.5">
                                <span class="text-forest-600 dark:text-forest-400 font-semibold">{{ $rp->category->name ?? '' }}</span>
                                <span class="w-1 h-1 rounded-full bg-urban-300 dark:bg-urban-600"></span>
                                <span>{{ $rp->created_at->translatedFormat('d M Y') }}</span>
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Terbaru --}}
            <div>
                <h3 class="font-display font-bold text-xl mb-6 dark:text-white text-urban-950 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Baru Saja Rilis
                </h3>
                <div class="space-y-6">
                    @foreach($latestPosts as $rp)
                    <a href="{{ route('blog.show', $rp->slug) }}" class="group flex gap-4 items-start">
                        <div class="shrink-0 w-24 h-20 rounded-xl overflow-hidden bg-urban-100 dark:bg-urban-800 border dark:border-urban-800/50 border-urban-200">
                            <img src="{{ $rp->asset_url }}" alt="{{ $rp->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm dark:text-urban-200 text-urban-800 group-hover:text-forest-600 dark:group-hover:text-forest-400 transition-colors line-clamp-2 leading-snug">{{ $rp->title }}</h4>
                            <p class="text-[11px] font-medium dark:text-urban-500 text-urban-500 mt-1.5 flex items-center gap-1.5">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wider">Baru</span>
                                <span class="w-1 h-1 rounded-full bg-urban-300 dark:bg-urban-600"></span>
                                <span>{{ $rp->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
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

    // Copy Share Link Function
    function copyShareLink(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link artikel berhasil disalin!');
            });
        } else {
            var input = document.createElement('input');
            input.setAttribute('value', text);
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('Link artikel berhasil disalin!');
        }
    }
</script>
@endsection
