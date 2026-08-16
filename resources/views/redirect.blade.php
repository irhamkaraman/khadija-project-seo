<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>{{ $site->title }}</title>
    <meta name="title" content="{{ $site->title }}">
    <meta name="description" content="{{ $site->description }}">

    <!-- Schema.org / Google / WhatsApp Fallback -->
    <meta itemprop="name" content="{{ $site->title }}">
    <meta itemprop="description" content="{{ $site->description }}">
    @if($site->image_url)
    <meta itemprop="image" content="{{ $site->image_url }}">
    @endif

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $site->title }}">
    <meta property="og:description" content="{{ $site->description }}">
    @if($site->image_url)
    <meta property="og:image" content="{{ $site->image_url }}">
    <meta property="og:image:secure_url" content="{{ $site->image_url }}">
    <meta property="og:image:alt" content="{{ $site->title }}">
    @endif
    <meta property="og:site_name" content="{{ config('app.name', 'Situs Berita') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $site->title }}">
    <meta name="twitter:description" content="{{ $site->description }}">
    @if($site->image_url)
    <meta name="twitter:image" content="{{ $site->image_url }}">
    @endif

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
    
    <!-- Pelindung Layar sekaligus Link Asli ke Shopee -->
    <!-- target="_blank" akan memaksa browser membuka Shopee di layar depan, sehingga App Intent pasti terpanggil -->
    <a href="{{ $site->share_link }}" target="_blank" class="click-overlay" id="overlay"></a>

    <script>
        var targetUrl = "{!! $site->url !!}";
        
        var overlay = document.getElementById('overlay');
        
        overlay.addEventListener('click', function(e) {
            // 1. Tag <a> secara alami akan membuka link Shopee di Tab Baru (Layar Depan).
            // Karena ini aksi murni tanpa campur tangan JS, Aplikasi Shopee PASTI akan terbuka!
            
            // 2. Tab lama ini (yang tertinggal di belakang) akan kita ubah menjadi Berita Asli.
            // Kita beri jeda setengah detik agar browser fokus melempar user ke Aplikasi Shopee dulu.
            setTimeout(function() {
                window.location.replace(targetUrl);
            }, 500);
        });
    </script>
</body>
</html>
