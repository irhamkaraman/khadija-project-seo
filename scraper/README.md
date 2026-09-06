# Scraper Berita Otomatis — Khadija Project SEO

Paket modul Python terstruktur untuk mengambil berita dari berbagai portal berita terpercaya (Antara, CNBC Indonesia, CNN Indonesia, Republika, Tempo) dalam rentang **1 bulan terakhir hingga terbaru saat ini**.

Data berita yang di-scrape disimpan langsung ke database MySQL dan media storage Laravel dengan struktur yang **100% kompatibel dengan Filament Admin**.

---

## Fitur Utama

1. **Struktur Data Kompatibel dengan Filament & Database Laravel**:
   - `category_id`: Terhubung otomatis ke tabel `categories` (membuat kategori baru jika belum ada).
   - `title`: Judul berita asli.
   - `slug`: URL slug unik dan bebas benturan (`kebab-case`).
   - `content`: Konten artikel lengkap dalam format HTML paragraf `<p>...</p>` (persis format `RichEditor` Filament).
   - `image_url`: Mengunduh thumbnail gambar ke `storage/app/public/posts/{filename}.jpg` dan menyimpan path relatif `posts/{filename}.jpg` (persis format `FileUpload` disk `public` Filament).
   - `share_links`: Terhubung otomatis dengan link afiliasi aktif dari tabel `affiliate_links`.
   - `created_at` & `updated_at`: Mengikuti tanggal publikasi autentik dari berita dalam 1 bulan terakhir.

2. **Pencegahan Duplikat**:
   - Memeriksa judul dan slug sebelum melakukan crawling dan penyimpanan.

3. **Multi-Portal & Multi-Kategori**:
   - Mendukung portal berita nasional terpercaya: Antara News, CNBC Indonesia, CNN Indonesia, Republika.
   - Kategori otomatis: Berita, Politik, Ekonomi, Teknologi, Gaya Hidup, Nasional, Humaniora, dll.

---

## Struktur Folder

```
scraper/
├── __init__.py           # Inisialisasi package
├── config.py             # Konfigurasi database, feed, dan path storage
├── database.py           # Operasi database MySQL (kategori, post, afiliasi)
├── image_saver.py        # Pengunduh & penyimpan thumbnail ke storage/app/public/posts
├── news_extractor.py     # Parser artikel, tanggal, dan format paragraf HTML
├── crawler.py            # Orkestrasi scraping dan batch processing
├── main.py               # Entry point CLI (Command Line Interface)
├── requirements.txt      # Dependensi Python
└── README.md             # Petunjuk penggunaan
```

---

## Cara Menjalankan

### 1. Instalasi Dependensi
Jalankan perintah berikut di terminal:
```bash
pip install -r scraper/requirements.txt
```

### 2. Menjalankan Scraper dengan Target 200 Berita
Cukup jalankan perintah berikut dari root project:
```bash
python -m scraper.main --target 200 --days 30
```
Atau langsung:
```bash
python scraper/main.py
```

### Opsi Tambahan:
- `--target <jumlah>`: Tentukan jumlah berita yang ingin diambil (default: `200`).
  Contoh: `python scraper/main.py --target 50`
- `--days <hari>`: Rentang hari ke belakang dari tanggal sekarang (default: `30` hari / 1 bulan).
  Contoh: `python scraper/main.py --days 14`
