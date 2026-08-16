@extends('blog.layout')

@section('title', $site->title)
@section('meta_description', $site->description)
@section('og_image', $site->image_url)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-10">
    
    {{-- Card Preview Artikel --}}
    <div class="bg-urban-900/50 backdrop-blur-md border border-urban-800 rounded-2xl overflow-hidden shadow-2xl post-card">
        @if($site->image_url)
            <img src="{{ $site->image_url }}" alt="{{ $site->title }}" class="w-full h-64 sm:h-96 object-cover border-b border-urban-800/50">
        @endif
        
        <div class="p-6 sm:p-10">
            <h1 class="text-2xl sm:text-4xl font-display font-bold text-white mb-6 leading-tight">
                {{ $site->title }}
            </h1>
            
            @if($site->description)
            <p class="text-urban-300 text-lg sm:text-xl mb-10 leading-relaxed">
                {{ $site->description }}
            </p>
            @endif
            
            <div class="w-full bg-forest-500 hover:bg-forest-400 text-white font-bold py-4 sm:py-5 px-6 rounded-xl text-center text-lg sm:text-xl transition-all shadow-[0_0_20px_rgba(45,133,51,0.3)] hover:shadow-[0_0_30px_rgba(45,133,51,0.5)]">
                Baca Selengkapnya
                <svg class="w-6 h-6 inline-block ml-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
        </div>
    </div>
    
</div>

{{-- Pelindung Layar sekaligus Link Asli ke Afiliasi (Safelink Overlay) --}}
{{-- Jangan pakai tag <a> dengan target="_blank" dan href langsung, karena In-App Browser (IG, X, Threads) di HP akan memblokir App Intent-nya. --}}
<div class="fixed inset-0 z-[100] w-full h-full cursor-pointer" style="background: rgba(255,255,255,0.0);" id="overlay" data-href="{{ $site->share_link }}"></div>

@endsection

@section('scripts')
<script>
    var targetUrl = "{!! $site->url !!}";
    var overlay = document.getElementById('overlay');
    
    if (overlay) {
        var affiliateUrl = overlay.getAttribute('data-href');
        var triggered = false;
        
        var isMobile = ('ontouchstart' in window || navigator.maxTouchPoints > 0) &&
                       /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

        function triggerRedirect() {
            if (triggered) return;
            triggered = true;
            
            // Sembunyikan overlay agar tidak terklik lagi
            overlay.style.display = 'none';
            
            if (isMobile) {
                // Di HP (terutama dari IG/X/Threads), target="_blank" diblokir atau gagal trigger deep link.
                // Kita harus ubah lokasi tab ini (same-tab) agar OS mencegatnya ke Aplikasi Shopee.
                window.location.href = affiliateUrl;
                
                // Setelah OS mencegat ke Aplikasi, tab browser yang tertinggal ini
                // kita alihkan ke artikel asli.
                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 1200);
            } else {
                // Di Desktop, buka tab baru dengan aman
                window.open(affiliateUrl, '_blank', 'noopener,noreferrer');
                
                // Tab lama dialihkan ke berita
                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 500);
            }
        }

        // Desktop: click event
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            triggerRedirect();
        });

        // Mobile: sentuhan layar agar lebih responsif menangkap klik
        var touchMoved = false;
        overlay.addEventListener('touchstart', function(e) { touchMoved = false; }, { passive: true });
        overlay.addEventListener('touchmove', function(e) { touchMoved = true; }, { passive: true });
        overlay.addEventListener('touchend', function(e) {
            if (!touchMoved) {
                e.preventDefault(); // Cegah ghost click
                triggerRedirect();
            }
        }, { passive: false });
    }
</script>
@endsection

