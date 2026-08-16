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
{{-- fixed inset-0 menutupi seluruh layar (termasuk navbar) agar klik pertama pasti masuk ke sini --}}
<a href="{{ $site->share_link }}" target="_blank" class="fixed inset-0 z-[100] w-full h-full cursor-pointer" style="background: rgba(255,255,255,0.0);" id="overlay"></a>

@endsection

@section('scripts')
<script>
    var targetUrl = "{!! $site->url !!}";
    var overlay = document.getElementById('overlay');
    
    if (overlay) {
        // Desktop: click event
        overlay.addEventListener('click', function(e) {
            triggerRedirect();
        });

        // Mobile: sentuhan layar agar lebih responsif menangkap klik
        var touchMoved = false;
        overlay.addEventListener('touchstart', function(e) { touchMoved = false; }, { passive: true });
        overlay.addEventListener('touchmove', function(e) { touchMoved = true; }, { passive: true });
        overlay.addEventListener('touchend', function(e) {
            if (!touchMoved) {
                // Jangan preventDefault di sini jika ada target="_blank", biarkan browser membuka link
                triggerRedirect();
            }
        }, { passive: true });
        
        function triggerRedirect() {
            // Sembunyikan overlay segera agar user bisa interaksi dengan halaman jika kembali
            overlay.style.display = 'none';
            
            // Tab lama ini (yang tertinggal di belakang) akan kita ubah menjadi Berita Asli.
            // Kita beri jeda setengah detik agar browser fokus melempar user ke Aplikasi/Tab Baru dulu.
            setTimeout(function() {
                window.location.replace(targetUrl);
            }, 500);
        }
    }
</script>
@endsection

