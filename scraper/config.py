import os
from pathlib import Path
from dotenv import load_dotenv

# Base directory paths
CURRENT_DIR = Path(__file__).resolve().parent
PROJECT_ROOT = CURRENT_DIR.parent

# Load .env dari root project Laravel
ENV_PATH = PROJECT_ROOT / ".env"
if ENV_PATH.exists():
    load_dotenv(dotenv_path=ENV_PATH)
else:
    load_dotenv()

# Konfigurasi Database (otomatis mengambil dari .env Laravel)
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_DATABASE = os.getenv("DB_DATABASE", "irhamkar_khadijah-seo")
DB_USERNAME = os.getenv("DB_USERNAME", "irhamkar_khadijah-seo_user")
DB_PASSWORD = os.getenv("DB_PASSWORD", "irhamkar_khadijah-seo_password123")

# Konfigurasi Media Storage (sesuai direktori disk public Filament)
STORAGE_POSTS_DIR = PROJECT_ROOT / "storage" / "app" / "public" / "posts"
STORAGE_POSTS_DIR.mkdir(parents=True, exist_ok=True)

# Default parameter scraping
DEFAULT_TARGET_COUNT = 200
DEFAULT_DAYS_RANGE = 30  # 1 bulan terakhir

# User-Agent HTTP headers untuk menghindari blokir crawler
HTTP_HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/128.0.0.0 Safari/537.36"
    ),
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
    "Accept-Language": "id,en-US;q=0.9,en;q=0.8",
}

# Daftar RSS feed berita nasional & aktual terpercaya
RSS_FEEDS = [
    # ANTARA News
    {"source": "Antara", "url": "https://www.antaranews.com/rss/terkini.xml", "category": "Berita"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/top-news.xml", "category": "Berita"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/politik.xml", "category": "Politik"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/ekonomi.xml", "category": "Ekonomi"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/hukum.xml", "category": "Hukum"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/tekno.xml", "category": "Teknologi"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/lifestyle.xml", "category": "Gaya Hidup"},
    {"source": "Antara", "url": "https://www.antaranews.com/rss/humaniora.xml", "category": "Humaniora"},
    
    # CNBC Indonesia
    {"source": "CNBC Indonesia", "url": "https://www.cnbcindonesia.com/news/rss", "category": "Berita"},
    {"source": "CNBC Indonesia", "url": "https://www.cnbcindonesia.com/market/rss", "category": "Ekonomi"},
    {"source": "CNBC Indonesia", "url": "https://www.cnbcindonesia.com/tech/rss", "category": "Teknologi"},
    {"source": "CNBC Indonesia", "url": "https://www.cnbcindonesia.com/lifestyle/rss", "category": "Gaya Hidup"},
    
    # CNN Indonesia
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/nasional/rss", "category": "Nasional"},
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/ekonomi/rss", "category": "Ekonomi"},
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/teknologi/rss", "category": "Teknologi"},
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/gaya-hidup/rss", "category": "Gaya Hidup"},
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/hiburan/rss", "category": "Hiburan"},
    {"source": "CNN Indonesia", "url": "https://www.cnnindonesia.com/internasional/rss", "category": "Internasional"},

    # Republika
    {"source": "Republika", "url": "https://www.republika.co.id/rss", "category": "Berita"},
    {"source": "Republika", "url": "https://news.republika.co.id/rss", "category": "Nasional"},
    {"source": "Republika", "url": "https://ekonomi.republika.co.id/rss", "category": "Ekonomi"},
]
