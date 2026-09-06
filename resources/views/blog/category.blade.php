@extends('blog.layout')

@section('title', $category->name)
@section('seo_title', 'Kategori: ' . $category->name . ' | ' . config('app.name'))
@section('meta_description', 'Kumpulan artikel dan berita terbaru dalam kategori ' . $category->name . ' di ' . config('app.name') . '. Selalu update setiap hari.')
@section('meta_keywords', $category->name . ', berita ' . $category->name . ', artikel ' . $category->name . ', ' . config('app.name'))
@section('canonical', route('blog.category', $category->slug))
@section('og_type', 'website')
@section('og_title', 'Kategori: ' . $category->name . ' | ' . config('app.name'))
@section('og_description', 'Baca semua artikel terbaru dalam kategori ' . $category->name . ' di ' . config('app.name') . '.')
@section('og_url', route('blog.category', $category->slug))

@section('json_ld')
@php
    $breadcrumbJson = json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda',            'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $category->name,      'item' => route('blog.category', $category->slug)],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp
<script type="application/ld+json">{!! $breadcrumbJson !!}</script>
@endsection

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-10 pt-8">
    <div class="max-w-6xl mx-auto">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm dark:text-urban-400 text-urban-600 mb-8">
            <a href="{{ route('home') }}" class="hover:text-forest-600 dark:hover:text-forest-400 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Beranda
            </a>
            <svg class="w-3.5 h-3.5 dark:text-urban-600 text-urban-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-forest-600 dark:text-forest-400 font-semibold">{{ $category->name }}</span>
        </nav>

        {{-- Title Block --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl dark:bg-forest-950/60 bg-forest-100/90 dark:border-forest-700/40 border-forest-300/80 flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-6 h-6 text-forest-600 dark:text-forest-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-forest-600 dark:text-forest-400 mb-0.5">Kategori</p>
                <h1 class="font-display text-3xl sm:text-4xl font-black dark:text-white text-urban-950">{{ $category->name }}</h1>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <div class="h-px flex-1 bg-gradient-to-r dark:from-forest-800/60 from-forest-200 to-transparent"></div>
            <span class="text-xs dark:text-urban-400 text-urban-600 font-medium">{{ $posts->total() }} Artikel</span>
        </div>
    </div>
</section>

{{-- ===== POSTS GRID ===== --}}
<section class="px-4 sm:px-6 lg:px-8 pb-20">
    <div class="max-w-6xl mx-auto">

        @if($posts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($posts as $post)
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
            {{ $posts->links() }}
        </div>

        @else
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <svg class="w-16 h-16 text-urban-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="font-display text-2xl font-bold text-urban-500 mb-2">Belum Ada Artikel</h3>
            <p class="text-urban-600">Belum ada artikel di kategori ini.</p>
        </div>
        @endif

    </div>
</section>

@endsection
