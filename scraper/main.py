import sys
import argparse
from datetime import datetime

from pathlib import Path

# Pastikan console Windows mendukung output karakter UTF-8
if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

# Pastikan direktori project root ada di sys.path
PROJECT_ROOT = Path(__file__).resolve().parent.parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

from scraper.config import (
    DEFAULT_TARGET_COUNT,
    DEFAULT_DAYS_RANGE,
    DB_HOST,
    DB_DATABASE,
    STORAGE_POSTS_DIR
)
from scraper.database import DatabaseManager
from scraper.crawler import NewsCrawler

def main():
    parser = argparse.ArgumentParser(
        description="Scraper Berita Otomatis untuk Khadija Project SEO (Laravel & Filament Compatible)"
    )
    parser.add_argument(
        "--target",
        type=int,
        default=DEFAULT_TARGET_COUNT,
        help=f"Jumlah target berita yang ingin diambil (default: {DEFAULT_TARGET_COUNT})"
    )
    parser.add_argument(
        "--days",
        type=int,
        default=DEFAULT_DAYS_RANGE,
        help=f"Rentang hari ke belakang untuk artikel yang diambil (default: {DEFAULT_DAYS_RANGE} hari)"
    )

    args = parser.parse_args()

    print("=" * 70)
    print("      KHADIJA PROJECT SEO — AUTOMATED NEWS SCRAPER PIPELINE")
    print("=" * 70)
    print(f" Target Berita    : {args.target} artikel")
    print(f" Rentang Waktu    : {args.days} hari terakhir hingga saat ini")
    print(f" Database Target  : {DB_HOST} / {DB_DATABASE}")
    print(f" Storage Gambar   : {STORAGE_POSTS_DIR}")
    print("=" * 70)

    try:
        db = DatabaseManager()
        stats_before = db.get_stats()
        print(f"[*] Status database saat ini : {stats_before['total_posts']} berita, {stats_before['total_categories']} kategori.")

        crawler = NewsCrawler(
            db=db,
            target_count=args.target,
            days_range=args.days
        )

        total_scraped = crawler.run()

        stats_after = db.get_stats()
        print("\n" + "=" * 70)
        print("                  RINGKASAN HASIL SCRAPING")
        print("=" * 70)
        print(f" Berita Baru Disimpan  : +{total_scraped} artikel")
        print(f" Total Berita Sekarang : {stats_after['total_posts']} artikel")
        print(f" Total Kategori        : {stats_after['total_categories']} kategori")
        print("=" * 70)
        print("[✓] Semua artikel dan thumbnail siap ditampilkan di Frontend dan Filament Admin!")

        db.close()

    except KeyboardInterrupt:
        print("\n[!] Scraping dihentikan oleh pengguna.")
        sys.exit(0)
    except Exception as e:
        print(f"\n[!] Terjadi kesalahan fatal: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
