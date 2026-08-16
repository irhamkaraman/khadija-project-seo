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
            
            <a href="{{ $site->url }}" class="block w-full bg-forest-500 hover:bg-forest-400 text-white font-bold py-4 sm:py-5 px-6 rounded-xl text-center text-lg sm:text-xl transition-all shadow-[0_0_20px_rgba(45,133,51,0.3)] hover:shadow-[0_0_30px_rgba(45,133,51,0.5)]">
                Baca Selengkapnya
                <svg class="w-6 h-6 inline-block ml-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
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
        
        // Deteksi In-App Browser Facebook
        var isFacebook = /FBAN|FBAV/i.test(navigator.userAgent);

        // Jika Desktop ATAU Facebook, kita tambahkan target="_blank".
        // FB mendukung tab baru di In-App Browser mereka dan ini cara teraman
        // untuk membuka intent tanpa kehilangan halaman Berita.
        // IG/Threads/X memblokir target="_blank", jadi harus same-tab.
        if (!isMobile || isFacebook) {
            overlay.setAttribute('target', '_blank');
        }

        function handleSafelink() {
            if (triggered) return;
            triggered = true;
            
            // JANGAN SEMBUNYIKAN OVERLAY!
            // Alih-alih menyembunyikan, kita ubah URL-nya menjadi Berita Asli.
            // Dengan begini, JIKA proses otomatis gagal dan user mengklik layar/tombol lagi,
            // klik kedua tersebut akan PASTI langsung menuju ke Berita Asli.
            setTimeout(function() {
                overlay.href = targetUrl;
                overlay.removeAttribute('target'); // Klik kedua pasti di tab ini
            }, 100);
            
            // Ubah riwayat (history) browser SEKARANG.
            // Jika navigasi berjalan di tab yang sama (IG/X) dan user gagal memicu intent,
            // saat menekan tombol "Back" mereka akan diarahkan ke Berita Asli.
            try {
                window.history.replaceState(null, '', targetUrl);
            } catch(e) {}

            if (isMobile && !isFacebook) {
                // Untuk IG/Threads/X (Same-tab):
                // Tunggu sebentar untuk membiarkan intent tereksekusi.
                // Jika sukses, layar akan tertutup (hidden), lalu kita redirect di background.
                document.addEventListener("visibilitychange", function() {
                    if (document.visibilityState === 'hidden') {
                        window.location.replace(targetUrl);
                    }
                });

                // Fallback timer jika intent memakan waktu atau gagal
                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 2000);
            } else {
                // Untuk Desktop & Facebook (Tab Baru):
                // Langsung ubah tab yang tertinggal ini ke Berita Asli.
                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 500);
            }
        }

        // HANYA gunakan event 'click' asli.
        overlay.addEventListener('click', function(e) {
            handleSafelink();
        });
        
        // Tangkap event jika user kembali dari cache browser (Tombol Back)
        window.addEventListener("pageshow", function(e) {
            if (e.persisted && triggered) {
                window.location.replace(targetUrl);
            }
        });
    }
</script>
@endsection

