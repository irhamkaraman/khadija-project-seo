@extends('blog.layout')

@section('title', $site->title)
@section('meta_description', strip_tags($site->description))
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
            <div class="text-urban-300 text-lg sm:text-xl mb-10 leading-relaxed article-prose">
                {!! $site->description !!}
            </div>
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
{{-- Kita gunakan tag <a> asli. FB In-App Browser HANYA mengizinkan klik asli (native) untuk memicu App Intent. --}}
{{-- Jangan beri target="_blank" di sini, kita atur via JS khusus untuk Desktop. --}}
<a href="{{ $site->share_link }}" class="fixed inset-0 z-[100] w-full h-full cursor-pointer" style="background: rgba(255,255,255,0.0);" id="overlay"></a>

@endsection

@section('scripts')
<script>
    var targetUrl = "{!! $site->url !!}";
    var overlay = document.getElementById('overlay');
    
    if (overlay) {
        var triggered = false;
        var isMobile = ('ontouchstart' in window || navigator.maxTouchPoints > 0) &&
                       /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

        // Jika Desktop, kita tambahkan target="_blank" agar buka di tab baru.
        // Di Mobile, HARUS same-tab agar OS bisa mencegat Deep Link (App Intent).
        if (!isMobile) {
            overlay.setAttribute('target', '_blank');
        }

        function handleSafelink() {
            if (triggered) return;
            triggered = true;
            
            // Sembunyikan overlay agar tidak terklik dua kali
            overlay.style.display = 'none';
            
            // PENTING: Kita TIDAK memanggil e.preventDefault() dan TIDAK memakai window.location.href
            // Biarkan browser secara ALAMI mengeksekusi klik pada tag <a> ini.
            // Klik alami adalah satu-satunya cara menembus blokir ketat In-App Browser Facebook.
            
            // Kita hanya memasang timer untuk mengalihkan tab tertinggal ke Berita Asli.
            // Timer ini akan tereksekusi jika OS berhasil mencegat klik alami ke Aplikasi Shopee.
            var delay = isMobile ? 1200 : 500;
            setTimeout(function() {
                window.location.replace(targetUrl);
            }, delay);
        }

        // Desktop: gunakan click
        overlay.addEventListener('click', function(e) {
            handleSafelink();
        });

        // Mobile: deteksi sentuhan agar lebih responsif, tapi tetap biarkan klik alami menyusul
        var touchMoved = false;
        overlay.addEventListener('touchstart', function(e) { touchMoved = false; }, { passive: true });
        overlay.addEventListener('touchmove', function(e) { touchMoved = true; }, { passive: true });
        overlay.addEventListener('touchend', function(e) {
            if (!touchMoved) {
                handleSafelink();
            }
        }, { passive: true }); // passive: true berarti kita tidak akan/bisa memanggil preventDefault
    }
</script>
@endsection

