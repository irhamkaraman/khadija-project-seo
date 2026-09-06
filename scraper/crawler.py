import time
import feedparser
from datetime import datetime, timezone
from typing import Optional, Callable
from scraper.config import RSS_FEEDS, DEFAULT_TARGET_COUNT, DEFAULT_DAYS_RANGE
from scraper.database import DatabaseManager
from scraper.image_saver import download_and_save_image
from scraper.news_extractor import (
    parse_published_date,
    is_within_last_days,
    extract_image_from_entry,
    fetch_full_article_content
)

class NewsCrawler:
    """
    Crawler utama yang menelusuri berbagai RSS feed berita,
    mengambil konten lengkap, mengunduh thumbnail, dan menyimpannya ke database.
    """

    def __init__(
        self,
        db: DatabaseManager,
        target_count: int = DEFAULT_TARGET_COUNT,
        days_range: int = DEFAULT_DAYS_RANGE,
        on_progress: Optional[Callable[[int, int, str], None]] = None
    ):
        self.db = db
        self.target_count = target_count
        self.days_range = days_range
        self.on_progress = on_progress
        self.saved_count = 0
        self.skipped_count = 0
        self.failed_count = 0

    def run(self) -> int:
        print(f"[*] Memulai crawling berita...")
        print(f"[*] Target: {self.target_count} berita (rentang {self.days_range} hari terakhir)")

        for feed_info in RSS_FEEDS:
            if self.saved_count >= self.target_count:
                break

            source_name = feed_info["source"]
            feed_url = feed_info["url"]
            category_name = feed_info["category"]

            print(f"\n[+] Membaca feed: {source_name} - {category_name} ({feed_url})")

            try:
                parsed_feed = feedparser.parse(feed_url)
            except Exception as e:
                print(f"[-] Gagal membaca feed {feed_url}: {e}")
                continue

            entries = parsed_feed.entries or []
            print(f"    Ditemukan {len(entries)} artikel di feed ini.")

            for entry in entries:
                if self.saved_count >= self.target_count:
                    break

                title = getattr(entry, "title", "").strip()
                link = getattr(entry, "link", "").strip()

                if not title or not link:
                    continue

                # 1. Periksa tanggal publikasi (1 bulan terakhir hingga saat ini)
                pub_date = parse_published_date(entry)
                if not is_within_last_days(pub_date, self.days_range):
                    self.skipped_count += 1
                    continue

                # 2. Cek apakah sudah ada di database
                base_slug = self.db._slugify(title)
                if not base_slug:
                    continue

                if self.db.post_exists(base_slug, title):
                    self.skipped_count += 1
                    continue

                # 3. Ambil isi konten lengkap & thumbnail
                content_html, og_image = fetch_full_article_content(link)

                # Fallback jika fetch HTML belum lengkap: gunakan summary dari RSS feed
                if len(content_html) < 80:
                    summary = getattr(entry, "summary", "") or getattr(entry, "description", "")
                    if summary:
                        # Bersihkan tag HTML kasar dan bungkus dalam <p>
                        import re
                        clean_summary = re.sub(r"<[^>]+>", "", summary).strip()
                        if clean_summary:
                            content_html = f"<p>{clean_summary}</p>"

                if len(content_html) < 80:
                    self.failed_count += 1
                    continue

                # 4. Ambil URL gambar
                image_url = extract_image_from_entry(entry) or og_image

                # 5. Unduh dan simpan gambar ke storage/app/public/posts
                saved_image_path = None
                if image_url:
                    saved_image_path = download_and_save_image(image_url)

                # 6. Dapatkan atau buat kategori
                cat_id = self.db.get_or_create_category(category_name)

                # 7. Pastikan slug unik
                final_slug = self.db.make_unique_slug(base_slug)

                # 8. Simpan ke database posts
                try:
                    post_id = self.db.insert_post(
                        category_id=cat_id,
                        title=title,
                        slug=final_slug,
                        content=content_html,
                        image_url=saved_image_path,
                        created_at=pub_date or datetime.now(timezone.utc),
                        updated_at=pub_date or datetime.now(timezone.utc)
                    )

                    self.saved_count += 1
                    print(f"    [{self.saved_count}/{self.target_count}] [ID:{post_id}] {title[:65]}...")

                    if self.on_progress:
                        self.on_progress(self.saved_count, self.target_count, title)

                    # Jeda sedikit agar sopan terhadap server sumber berita
                    time.sleep(0.3)

                except Exception as e:
                    print(f"    [-] Gagal menyimpan '{title[:30]}': {e}")
                    self.failed_count += 1

        print(f"\n[✓] Crawling selesai!")
        print(f"    Berhasil disimpan: {self.saved_count} berita")
        print(f"    Dilewati (duplikat / luar tanggal): {self.skipped_count}")
        print(f"    Gagal: {self.failed_count}")

        return self.saved_count
