@extends('blog.layout')

@section('title', 'Beranda')
@section('seo_title', config('app.name') . ' — Portal Berita & Informasi Terkini')
@section('meta_description', 'Baca berita dan informasi terkini di ' . config('app.name') . '. Temukan artikel pilihan seputar gaya hidup, teknologi, dan kabar terbaru yang terpercaya.')
@section('meta_keywords', 'berita terbaru, informasi terkini, artikel pilihan, portal berita, ' . config('app.name'))
@section('canonical', route('home'))
@section('og_type', 'website')
@section('og_title', config('app.name') . ' — Portal Berita & Informasi Terkini')
@section('og_description', 'Portal berita dan informasi terpercaya. Temukan artikel pilihan di ' . config('app.name') . '.')
@section('og_url', route('home'))

@section('extra_styles')
/* Sidebar popup desktop */
#affiliate-sidebar {
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
}
#affiliate-sidebar.hidden-sidebar {
    transform: translateX(120%) scale(0.95);
    opacity: 0;
    pointer-events: none;
}
#affiliate-sidebar.visible-sidebar {
    transform: translateX(0) scale(1);
    opacity: 1;
    pointer-events: all;
}

/* Safelink Overlay */
.safelink-overlay {
    position: fixed; top: 0; left: 0;
    width: 100vw; height: 100vh;
    z-index: 999999;
    cursor: default;
}
@endsection

@section('content')



@if($latestPosts->count() > 0)
<section class="px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-6xl mx-auto">

        {{-- Section header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-gradient-to-b from-forest-400 to-forest-600 rounded-full"></div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold dark:text-white text-urban-950">
                    Artikel Unggulan
                </h2>
            </div>
            <a href="{{ route('blog.index') }}"
               class="hidden sm:flex items-center gap-2 text-sm font-semibold dark:text-forest-400 text-forest-600 dark:hover:text-forest-300 hover:text-forest-700 transition-colors">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        @php $featured = $latestPosts->first(); @endphp

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Featured Large Card --}}
            <a href="{{ route('blog.show', $featured->slug) }}"
               class="post-card group relative lg:col-span-3 block rounded-3xl overflow-hidden shadow-2xl shadow-black/30"
               style="min-height: 420px; position: relative;">

                @if($featured->image_url)
                    <img src="{{ url('/file/' . $featured->image_url) }}"
                         alt="{{ $featured->title }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         style="z-index: 1;">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-forest-800 to-urban-900" style="z-index: 1;"></div>
                @endif

                {{-- Overlay Gradasi Gelap Penuh di atas gambar --}}
                <div class="absolute inset-0 pointer-events-none"
                     style="background: linear-gradient(to top, rgba(10, 13, 20, 0.95) 0%, rgba(10, 13, 20, 0.45) 50%, rgba(10, 13, 20, 0.15) 100%); z-index: 2;"></div>

                {{-- Content dengan Gradasi Kuat di Area Bawah --}}
                <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8"
                     style="background: linear-gradient(to top, rgba(10, 13, 20, 0.98) 0%, rgba(10, 13, 20, 0.9) 45%, rgba(10, 13, 20, 0.6) 75%, transparent 100%); padding-top: 6rem; z-index: 3;">
                    
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-white text-xs font-semibold uppercase tracking-wider shadow-md"
                              style="background-color: #1e6b24;">
                            {{ $featured->category->name ?? 'Artikel' }}
                        </span>
                        <span class="text-xs flex items-center gap-1 font-medium"
                              style="color: #cbd5e1; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $featured->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <h2 class="font-display text-2xl sm:text-3xl font-bold mb-3 leading-snug group-hover:text-forest-200 transition-colors line-clamp-3"
                        style="color: #ffffff; text-shadow: 0 2px 6px rgba(0,0,0,0.9);">
                        {{ $featured->title }}
                    </h2>

                    <p class="text-sm line-clamp-2 mb-4"
                       style="color: #e2e8f0; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                        {{ Str::limit(strip_tags($featured->content), 140) }}
                    </p>

                    <div class="inline-flex items-center gap-2 text-sm font-semibold group-hover:text-forest-200 transition-colors"
                         style="color: #86c489; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Sidebar 2 Cards --}}
            <div class="lg:col-span-2 flex flex-col gap-6">
                @foreach($latestPosts->skip(1)->take(2) as $post)
                <a href="{{ route('blog.show', $post->slug) }}"
                   class="post-card group flex flex-col sm:flex-row lg:flex-col rounded-2xl overflow-hidden dark:bg-urban-900/60 bg-white/90 dark:border dark:border-urban-800/40 border border-urban-200/80 backdrop-blur-sm shadow-md hover:shadow-xl flex-1 transition-all">

                    <div class="relative sm:w-40 lg:w-full h-44 sm:h-auto lg:h-44 flex-shrink-0 overflow-hidden">
                        @if($post->image_url)
                            <img src="{{ url('/file/' . $post->image_url) }}" alt="{{ $post->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-urban-800 to-urban-900 flex items-center justify-center">
                                <svg class="w-10 h-10 dark:text-urban-600 text-urban-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full dark:bg-forest-950/80 bg-white/95 dark:text-forest-300 text-forest-800 text-xs font-semibold uppercase tracking-wide shadow-md backdrop-blur-sm border dark:border-forest-800/40 border-forest-200/60">
                            {{ $post->category->name ?? 'Umum' }}
                        </span>
                    </div>

                    <div class="flex flex-col flex-1 p-5">
                        <div class="flex items-center gap-1.5 text-xs dark:text-urban-400 text-urban-600 mb-2 font-medium">
                            <svg class="w-3.5 h-3.5 dark:text-urban-500 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $post->created_at->translatedFormat('d M Y') }}
                        </div>
                        <h3 class="font-display font-bold text-base dark:text-white text-urban-950 mb-2 leading-snug group-hover:text-forest-600 dark:group-hover:text-forest-200 transition-colors line-clamp-3 flex-1">
                            {{ $post->title }}
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs font-semibold dark:text-forest-400 text-forest-600 group-hover:text-forest-700 dark:group-hover:text-forest-300 transition-colors mt-3">
                            Baca
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

        </div>

        {{-- Mobile "lihat semua" --}}
        <div class="mt-6 flex justify-center sm:hidden">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 dark:bg-urban-800/60 bg-white border dark:border-urban-700/40 border-urban-200 shadow-sm dark:text-urban-200 text-urban-800 font-semibold rounded-xl text-sm transition-all dark:hover:bg-urban-700/60 hover:bg-urban-50">
                Lihat Semua Artikel
                <svg class="w-4 h-4 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

    </div>
</section>
@endif

{{-- Placeholder Iklan Klasik AJAX --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="ajax-ad-slot"></div>
</div>

@if($morePosts->count() > 0)
<section class="px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-6xl mx-auto">

        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-gradient-to-b from-urban-400 to-urban-600 rounded-full"></div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold dark:text-white text-urban-950">
                    Artikel Terbaru
                </h2>
            </div>
            <a href="{{ route('blog.index') }}"
               class="hidden sm:flex items-center gap-2 text-sm font-semibold dark:text-forest-400 text-forest-600 dark:hover:text-forest-300 hover:text-forest-700 transition-colors">
                Semua Artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($morePosts as $post)
            <a href="{{ route('blog.show', $post->slug) }}"
               class="post-card group flex flex-col rounded-2xl overflow-hidden dark:bg-urban-900/60 bg-white/90 dark:border dark:border-urban-800/40 border border-urban-200/80 shadow-md hover:shadow-xl dark:shadow-black/30 backdrop-blur-sm transition-all">

                <div class="relative h-48 overflow-hidden flex-shrink-0">
                    @if($post->image_url)
                        <img src="{{ url('/file/' . $post->image_url) }}" alt="{{ $post->title }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-urban-800 to-urban-900 flex items-center justify-center">
                            <svg class="w-12 h-12 dark:text-urban-600 text-urban-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t dark:from-urban-900/80 from-black/30 to-transparent"></div>
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full dark:bg-forest-950/80 bg-white/95 dark:text-forest-300 text-forest-800 text-xs font-semibold uppercase tracking-wide shadow-md backdrop-blur-sm border dark:border-forest-800/40 border-forest-200/60">
                        {{ $post->category->name ?? 'Umum' }}
                    </span>
                </div>

                <div class="flex flex-col flex-1 p-5">
                    <div class="flex items-center gap-1.5 text-xs dark:text-urban-400 text-urban-600 mb-3 font-medium">
                        <svg class="w-3.5 h-3.5 dark:text-urban-500 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $post->created_at->translatedFormat('d M Y') }}
                    </div>
                    <h3 class="font-display font-bold text-base dark:text-white text-urban-950 mb-2 leading-snug group-hover:text-forest-600 dark:group-hover:text-forest-200 transition-colors line-clamp-2 flex-1">
                        {{ $post->title }}
                    </h3>
                    <p class="text-sm dark:text-urban-400 text-urban-700 line-clamp-2 leading-relaxed mb-4">
                        {{ Str::limit(strip_tags($post->content), 100) }}
                    </p>
                    <div class="pt-4 dark:border-t dark:border-urban-800/60 border-t border-urban-100 flex items-center gap-1.5 text-xs font-semibold dark:text-forest-400 text-forest-600 group-hover:text-forest-700 dark:group-hover:text-forest-300 transition-colors">
                        Baca Selengkapnya
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-3 px-8 py-4 dark:bg-urban-800/60 bg-white dark:hover:bg-urban-700/60 hover:bg-forest-50 dark:text-urban-200 text-urban-800 font-bold rounded-2xl text-sm transition-all dark:border dark:border-urban-700/40 border border-urban-200 shadow-md hover:border-forest-300 hover:text-forest-700">
                <svg class="w-5 h-5 text-forest-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Muat Lebih Banyak Artikel
            </a>
        </div>

        {{-- Slot Iklan 2: Di bawah Artikel Terbaru --}}
        <div class="ajax-ad-slot mt-10"></div>

    </div>
</section>
@endif

{{-- ================================================================ --}}
{{-- CATEGORIES SECTION --}}
{{-- ================================================================ --}}
@if($categories->count() > 0)
<section class="px-4 sm:px-6 lg:px-8 pb-20">
    <div class="max-w-6xl mx-auto">

        <div class="flex items-center gap-3 mb-8">
            <div class="w-1.5 h-8 bg-gradient-to-b from-forest-400 to-forest-600 rounded-full"></div>
            <h2 class="font-display text-2xl sm:text-3xl font-bold dark:text-white text-urban-950">
                Jelajahi Kategori
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-{{ min($categories->count(), 5) }} gap-4">
            @foreach($categories->take(5) as $cat)
            <a href="{{ route('blog.category', $cat->slug) }}"
               class="cat-card group relative overflow-hidden rounded-2xl p-5 sm:p-6 transition-all duration-300 dark:bg-urban-900/60 bg-white/90 dark:border dark:border-urban-800/60 border border-urban-200/80 shadow-md hover:shadow-xl dark:hover:border-forest-700/50 hover:border-forest-300 backdrop-blur-sm">

                {{-- Shimmer effect on hover --}}
                <div class="absolute inset-0 bg-gradient-to-br from-forest-500/0 via-forest-500/5 to-forest-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="relative">
                    <div class="w-10 h-10 rounded-xl dark:bg-forest-950/80 bg-forest-100/90 dark:border dark:border-forest-700/40 border border-forest-200 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 dark:text-forest-400 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-base sm:text-lg dark:text-white text-urban-950 mb-1 group-hover:text-forest-600 dark:group-hover:text-forest-300 transition-colors">
                        {{ $cat->name }}
                    </h3>
                    <p class="text-xs dark:text-urban-400 text-urban-600 flex items-center gap-1 font-medium">
                        {{ $cat->posts_count ?? $cat->posts()->count() }} artikel
                        <svg class="w-3.5 h-3.5 text-forest-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </p>
                </div>
            </a>
            @endforeach
        </div>

    </div>
</section>

{{-- Slot Iklan 3: Di bawah Kategori --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="ajax-ad-slot"></div>
</div>
@endif



{{-- ================================================================ --}}
{{-- CTA SECTION --}}
{{-- ================================================================ --}}
<section class="px-4 sm:px-6 lg:px-8 pb-20">
    <div class="max-w-4xl mx-auto">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-forest-700 via-forest-600 to-forest-800 p-8 sm:p-12 text-center shadow-2xl shadow-forest-900/40">

            {{-- Decorative blobs --}}
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-48 h-48 bg-black/10 rounded-full translate-x-1/4 translate-y-1/4 blur-2xl pointer-events-none"></div>

            <div class="relative">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-forest-100 text-xs font-semibold tracking-wider uppercase mb-6">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                    </svg>
                    Jangan Ketinggalan
                </div>

                <h2 class="font-display text-3xl sm:text-4xl font-black text-white mb-4 leading-tight">
                    Temukan Semua Berita<br>Terkini di Satu Tempat
                </h2>
                <p class="text-forest-200 text-lg mb-8 max-w-xl mx-auto leading-relaxed">
                    Ratusan artikel pilihan dari berbagai kategori siap menemani hari-hari Anda dengan informasi berkualitas.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('blog.index') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-forest-700 font-bold rounded-2xl text-base transition-all hover:bg-forest-50 shadow-lg shadow-forest-900/30 hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Mulai Membaca
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Slot Iklan 4: Di bawah CTA / Sebelum Footer --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="ajax-ad-slot"></div>
</div>

{{-- ================================================================ --}}
{{-- FLOATING AFFILIATE ADS (2 Ads Bottom Bar) --}}
{{-- ================================================================ --}}
@if($floatingAds->count() > 0)
<div id="floating-ads-footer" class="fixed bottom-0 left-0 right-0 z-50 p-2 md:p-4 pointer-events-none transition-transform duration-500 translate-y-full">
    <div class="max-w-4xl mx-auto pointer-events-auto relative">
        <div class="bg-white dark:bg-urban-900 rounded-t-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.15)] border-t border-l border-r border-urban-200 dark:border-urban-800 p-2 flex flex-col md:flex-row gap-2">
            {{-- Tombol tutup --}}
            <button id="close-floating-ads" class="absolute -top-3 -right-2 md:-right-3 w-8 h-8 rounded-full bg-urban-100 dark:bg-urban-800 text-urban-600 dark:text-urban-300 flex items-center justify-center shadow-lg hover:bg-red-500 hover:text-white transition-colors z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            @foreach($floatingAds as $ad)
            <a href="{{ route('affiliate.go', $ad->slug) }}" target="_self" class="flex-1 flex items-center gap-3 p-2 hover:bg-urban-50 dark:hover:bg-urban-800 rounded-xl transition-colors">
                @if($ad->image_url)
                <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-16 h-16 md:w-20 md:h-20 object-cover rounded-lg flex-shrink-0">
                @endif
                <div class="flex-1 min-w-0">
                    @if($ad->badge)
                    <span class="inline-block px-2 py-0.5 rounded text-white bg-red-500 text-[10px] font-bold mb-1">{{ $ad->badge }}</span>
                    @endif
                    <h4 class="text-xs md:text-sm font-bold text-urban-900 dark:text-white line-clamp-2 leading-tight">{{ $ad->title }}</h4>
                </div>
                <div class="flex-shrink-0 px-3 py-1.5 md:px-4 md:py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs md:text-sm font-bold rounded-lg whitespace-nowrap transition-colors">
                    {{ $ad->cta_text }}
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Safelink Overlay --}}
@if($floatingAds->count() > 0)
<div class="safelink-overlay" id="safelinkOverlay" data-href="{{ route('affiliate.go', $floatingAds->first()->slug) }}"></div>
@endif

@endsection

@section('scripts')
@if($floatingAds->count() > 0)
<script>
(function() {
    var floatingAds = document.getElementById('floating-ads-footer');
    var closeBtn = document.getElementById('close-floating-ads');

    // Menangani tampilan banner melayang (floating footer)
    var bannerShown = sessionStorage.getItem('affiliate_banner_shown');
    
    function showBanner() {
        if (floatingAds && !bannerShown) {
            bannerShown = true;
            sessionStorage.setItem('affiliate_banner_shown', '1');
            floatingAds.classList.remove('translate-y-full');
            floatingAds.classList.add('translate-y-0');
        }
    }

    function hideBanner() {
        if (floatingAds) {
            floatingAds.classList.remove('translate-y-0');
            floatingAds.classList.add('translate-y-full');
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', hideBanner);

    // Tampilkan banner segera setelah user mulai scroll sedikit
    var ticking = false;
    window.addEventListener('scroll', function() {
        if (ticking || bannerShown) return;
        ticking = true;
        requestAnimationFrame(function() {
            if (window.scrollY > 100) showBanner();
            ticking = false;
        });
    }, { passive: true });
    
    // Atau jika halaman langsung dimuat di tengah
    if (window.scrollY > 100) showBanner();

    // Safelink Trap (Sama persis dengan halaman blog/show)
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

        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            openAffiliate();
        });

        var touchMoved = false;
        overlay.addEventListener('touchstart', function(e) {
            touchMoved = false;
        }, { passive: true });

        overlay.addEventListener('touchmove', function(e) {
            touchMoved = true;
        }, { passive: true });

        overlay.addEventListener('touchend', function(e) {
            if (touchMoved) return; 
            e.preventDefault();    
            openAffiliate();
        }, { passive: false });
    }
})();
</script>
@endif
@endsection
