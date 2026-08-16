@extends('blog.layout')

@section('title', 'Artikel Terbaru')
@section('seo_title', 'Artikel Terbaru | ' . config('app.name'))
@section('meta_description', 'Baca artikel dan berita terbaru di ' . config('app.name') . '. Temukan informasi terpercaya seputar gaya hidup, teknologi, dan kabar terkini.')
@section('meta_keywords', 'berita terbaru, artikel, informasi terkini, ' . config('app.name'))
@section('canonical', route('blog.index'))
@section('og_type', 'website')
@section('og_title', 'Artikel Terbaru | ' . config('app.name'))
@section('og_description', 'Portal berita dan informasi terkini. Temukan artikel pilihan di ' . config('app.name') . '.')
@section('og_url', route('blog.index'))

@section('json_ld')
@php
    // Hindari {curly brace} mentah di dalam JSON-LD agar Blade tidak salah parse
    $jsonLdTarget = route('blog.index') . '?q={search_term_string}';
    $jsonLd = json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => config('app.name'),
        'url'      => config('app.url'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => $jsonLdTarget,
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp
<script type="application/ld+json">{!! $jsonLd !!}</script>
@endsection

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-12 pt-8">
    <div class="max-w-6xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-forest-900/60 border border-forest-700/40 text-forest-400 text-xs font-medium tracking-wider uppercase mb-6">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M17.293 3.707a1 1 0 00-1.414 0L10 9.586 4.121 3.707a1 1 0 00-1.414 1.414L8.586 11H3a1 1 0 100 2h5.586l-4.293 4.293a1 1 0 101.414 1.414L10 14.414l4.293 4.293a1 1 0 001.414-1.414L11.414 13H17a1 1 0 100-2h-5.586l5.293-5.293a1 1 0 000-1.414z" clip-rule="evenodd"/>
            </svg>
            Portal Informasi
        </div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-5">
            Artikel <span class="text-transparent bg-clip-text bg-gradient-to-r from-forest-400 to-forest-300">Terkini</span>
        </h1>
        <p class="text-urban-400 text-lg max-w-xl mx-auto">
            Temukan berita dan informasi terpercaya dari seluruh penjuru kota dan alam.
        </p>
    </div>
</section>

{{-- ===== POSTS GRID ===== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-20">
    <div class="max-w-6xl mx-auto">

        @if($posts->count() > 0)

            {{-- Featured Post (Pertama) --}}
            @php $featured = $posts->first(); @endphp
            <a href="{{ route('blog.show', $featured->slug) }}"
               class="post-card group relative block rounded-2xl overflow-hidden mb-8 border border-urban-800/40 hover:border-forest-700/50 shadow-xl shadow-black/40"
               style="min-height: 420px;">
                @if($featured->image_url)
                    <img src="{{ Storage::url($featured->image_url) }}" alt="{{ $featured->title }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-urban-800 to-urban-900"></div>
                @endif
                <div class="absolute inset-0 bg-card-gradient"></div>

                <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-10">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full bg-forest-600/80 text-forest-100 text-xs font-semibold uppercase tracking-wider">
                            {{ $featured->category->name ?? 'Artikel' }}
                        </span>
                        <span class="text-urban-400 text-xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $featured->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight group-hover:text-forest-200 transition-colors">
                        {{ $featured->title }}
                    </h2>
                    <p class="text-urban-300 text-sm lg:text-base line-clamp-2 mb-5">
                        {{ Str::limit(strip_tags($featured->content), 160) }}
                    </p>
                    <div class="inline-flex items-center gap-2 text-sm font-semibold text-forest-400 group-hover:text-forest-300 transition-colors">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Grid Sisanya --}}
            @if($posts->count() > 1)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($posts->skip(1) as $post)
                <a href="{{ route('blog.show', $post->slug) }}"
                   class="post-card group flex flex-col rounded-2xl overflow-hidden border border-urban-800/40 hover:border-forest-700/50 bg-urban-900/50 shadow-lg shadow-black/30">

                    <div class="relative h-48 overflow-hidden flex-shrink-0">
                        @if($post->image_url)
                            <img src="{{ Storage::url($post->image_url) }}" alt="{{ $post->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-urban-800 to-urban-900 flex items-center justify-center">
                                <svg class="w-12 h-12 text-urban-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-urban-900/80 to-transparent"></div>
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-forest-800/90 text-forest-300 text-xs font-semibold uppercase tracking-wide">
                            {{ $post->category->name ?? 'Umum' }}
                        </span>
                    </div>

                    <div class="flex flex-col flex-1 p-5">
                        <div class="flex items-center gap-1.5 text-xs text-urban-500 mb-3">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $post->created_at->translatedFormat('d M Y') }}
                        </div>
                        <h3 class="font-display font-bold text-base text-white mb-2 leading-snug group-hover:text-forest-200 transition-colors line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        <p class="text-urban-400 text-sm line-clamp-2 flex-1 leading-relaxed">
                            {{ Str::limit(strip_tags($post->content), 100) }}
                        </p>
                        <div class="mt-4 pt-4 border-t border-urban-800/60 flex items-center gap-1.5 text-xs font-semibold text-forest-500 group-hover:text-forest-400 transition-colors">
                            Baca Selengkapnya
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </div>

                </a>
                @endforeach
            </div>
            @endif

            {{-- Pagination --}}
            <div class="mt-10 flex justify-center">
                {{ $posts->links() }}
            </div>

        @else

            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <svg class="w-16 h-16 text-urban-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="font-display text-2xl font-bold text-urban-500 mb-2">Belum Ada Artikel</h3>
                <p class="text-urban-600">Artikel akan segera tersedia. Pantau terus!</p>
            </div>

        @endif

    </div>
</section>

@endsection
