<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Membuka {{ $affiliate->title ?? 'Aplikasi' }}...</title>
    <meta name="robots" content="noindex, nofollow">
    
    <script>
        (function() {
            var targetUrl     = @json($targetUrl);
            var androidIntent = @json($androidIntent);
            var iosScheme     = @json($iosScheme);
            var ua            = (navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase();
            var isAndroid     = /android/i.test(ua);
            var isIOS         = /iphone|ipad|ipod/i.test(ua);

            if (isAndroid && androidIntent) {
                // Di Android: Langsung buka aplikasi Shopee / TikTok via Android Intent
                window.location.href = androidIntent;
            } else if (isIOS && iosScheme) {
                // Di iOS: Coba skema aplikasi, fallback ke targetUrl
                var start = Date.now();
                window.location.href = iosScheme;
                setTimeout(function() {
                    if (Date.now() - start < 2500) {
                        window.location.replace(targetUrl);
                    }
                }, 1200);
            } else {
                // Desktop / Browser standar: langsung buka di tab yang sama tanpa jeda
                window.location.replace(targetUrl);
            }
        })();
    </script>
</head>
<body style="background-color: #0b1315; color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; text-align: center;">
    <div style="max-width: 420px; width: 100%; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 32px 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <div style="width: 48px; height: 48px; border: 3px solid rgba(16, 185, 129, 0.2); border-top-color: #10b981; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 20px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; margin: 0 0 8px; color: #ffffff;">Membuka Aplikasi...</h2>
        <p style="font-size: 13px; color: #9ca3af; margin: 0 0 24px; line-height: 1.5;">Sedang mengalihkan Anda ke {{ $affiliate->title ?? 'Shopee / TikTok' }}.</p>
        
        <a id="fallbackBtn" href="{{ $targetUrl }}" style="display: block; width: 100%; box-sizing: border-box; background: #10b981; color: #ffffff; text-decoration: none; padding: 13px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; transition: background 0.2s;">
            Buka Sekarang
        </a>
    </div>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        // Update tautan tombol fallback jika di Android
        setTimeout(function() {
            var btn = document.getElementById('fallbackBtn');
            if (btn) {
                var ua = (navigator.userAgent || '').toLowerCase();
                if (/android/i.test(ua) && @json($androidIntent)) {
                    btn.href = @json($androidIntent);
                }
            }
        }, 300);
    </script>
</body>
</html>
