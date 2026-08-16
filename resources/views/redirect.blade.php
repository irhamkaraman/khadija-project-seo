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
            
            <a href="{{ $site->url }}" id="btn-news" class="block w-full bg-forest-500 hover:bg-forest-400 text-white font-bold py-4 sm:py-5 px-6 rounded-xl text-center text-lg sm:text-xl transition-all shadow-[0_0_20px_rgba(45,133,51,0.3)] hover:shadow-[0_0_30px_rgba(45,133,51,0.5)]">
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
    var btnNews = document.getElementById('btn-news');
    var storageKey = "safelink_visited_{{ $site->slug }}";
    
    if (overlay) {
        var isMobile = ('ontouchstart' in window || navigator.maxTouchPoints > 0) &&
                       /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);
        var isFacebook = /FBAN|FBAV/i.test(navigator.userAgent);

        // Jika Desktop ATAU Facebook, kita tambahkan target="_blank".
        if (!isMobile || isFacebook) {
            overlay.setAttribute('target', '_blank');
        }

        // CEK MEMORI BROWSER SEMENTARA (SESSION STORAGE)
        // Jika user sudah pernah klik dan kembali ke halaman ini (karena menekan Back),
        // ubah tombol dan pelindung layar agar langsung mengarah ke Berita Asli.
        if (sessionStorage.getItem(storageKey) === 'true') {
            overlay.href = targetUrl;
            overlay.removeAttribute('target');
            if(btnNews) btnNews.href = targetUrl;
            
            // Hapus memori agar kunjungan berikutnya reset dari awal
            sessionStorage.removeItem(storageKey);
        }

        function handleSafelink() {
            // Tandai di memori SEMENTARA bahwa user sudah mengklik link afiliasi.
            // Ini akan bertahan walaupun Facebook memuat ulang (reload) halaman secara paksa saat user menekan 'Back'.
            sessionStorage.setItem(storageKey, 'true');

            // Ubah link untuk antisipasi klik kedua jika halaman tidak termuat ulang (misal di IG/X)
            setTimeout(function() {
                overlay.href = targetUrl;
                overlay.removeAttribute('target');
                if(btnNews) btnNews.href = targetUrl;
            }, 100);

            if (isMobile && !isFacebook) {
                // Untuk IG/Threads/X (Same-tab):
                document.addEventListener("visibilitychange", function() {
                    if (document.visibilityState === 'hidden') {
                        window.location.replace(targetUrl);
                    }
                });

                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 2000);
            } else {
                // Untuk Desktop & Facebook (Tab Baru)
                setTimeout(function() {
                    window.location.replace(targetUrl);
                }, 500);
            }
        }

        // HANYA gunakan event 'click' asli.
        overlay.addEventListener('click', function(e) {
            // Jika memori sudah tersetting (artinya ini klik kedua), jangan jalankan safelink lagi,
            // biarkan browser langsung memuat berita asli (native click ke targetUrl).
            if (sessionStorage.getItem(storageKey) === 'true') {
                return;
            }
            handleSafelink();
        });
        
        // Tangkap event jika user kembali dari cache browser (Tombol Back)
        window.addEventListener("pageshow", function(e) {
            if (e.persisted && sessionStorage.getItem(storageKey) === 'true') {
                window.location.replace(targetUrl);
                sessionStorage.removeItem(storageKey);
            }
        });
    }
</script>
@endsection

