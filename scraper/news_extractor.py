import re
import requests
import bs4
from datetime import datetime, timezone, timedelta
from typing import Optional, Dict, Any, Tuple
from dateutil import parser as date_parser
from scraper.config import HTTP_HEADERS

def parse_published_date(entry: Any) -> Optional[datetime]:
    """
    Mengekstrak dan mengonversi waktu rilis berita ke datetime UTC.
    """
    if hasattr(entry, "published_parsed") and entry.published_parsed:
        try:
            return datetime(*entry.published_parsed[:6], tzinfo=timezone.utc)
        except Exception:
            pass

    date_str = getattr(entry, "published", None) or getattr(entry, "updated", None)
    if not date_str:
        return None

    try:
        dt = date_parser.parse(date_str)
        if dt.tzinfo is None:
            dt = dt.replace(tzinfo=timezone.utc)
        return dt
    except Exception:
        return None

def is_within_last_days(dt: Optional[datetime], days: int = 30) -> bool:
    """
    Memvalidasi apakah berita berada dalam rentang N hari terakhir hingga sekarang.
    """
    if not dt:
        return True  # Jika tanggal tidak terbaca, izinkan untuk di-scrape

    now = datetime.now(timezone.utc)
    cutoff = now - timedelta(days=days + 5)  # buffer 5 hari untuk cakupan 1 bulan penuh
    return cutoff <= dt <= (now + timedelta(days=1))

def extract_image_from_entry(entry: Any, soup: Optional[bs4.BeautifulSoup] = None) -> Optional[str]:
    """
    Mencari URL gambar thumbnail dari enclosure, media_content, atau meta tag html.
    """
    # 1. Cek enclosure RSS
    if hasattr(entry, "enclosures") and entry.enclosures:
        for enc in entry.enclosures:
            href = enc.get("href")
            if href and any(ext in href.lower() for ext in [".jpg", ".jpeg", ".png", ".webp"]):
                return href

    # 2. Cek media_content RSS
    if hasattr(entry, "media_content") and entry.media_content:
        for media in entry.media_content:
            url = media.get("url")
            if url:
                return url

    # 3. Cek meta tag og:image di HTML
    if soup:
        og_img = soup.find("meta", property="og:image") or soup.find("meta", attrs={"name": "twitter:image"})
        if og_img and og_img.get("content"):
            return og_img["content"]

    return None

def fetch_full_article_content(url: str) -> Tuple[str, Optional[str]]:
    """
    Mengambil isi berita lengkap dari halaman URL artikel
    dan menghasilkan format HTML paragraf <p>...</p> yang bersih.
    """
    try:
        res = requests.get(url, headers=HTTP_HEADERS, timeout=12)
        if res.status_code != 200:
            return "", None

        soup = bs4.BeautifulSoup(res.text, "html.parser")

        # Hapus elemen pengganggu: script, style, iklan, komentar, nav, footer
        for tag in soup(["script", "style", "nav", "footer", "aside", "header", "noscript", "iframe"]):
            tag.decompose()

        for class_name in ["ads", "advertisement", "baca-juga", "related", "share-box", "tag-list", "author-box"]:
            for el in soup.find_all(attrs={"class": re.compile(class_name, re.I)}):
                el.decompose()

        # Selector konten spesifik untuk portal berita Indonesia populer
        content_container = None
        selectors = [
            "div.detail-text",         # CNN Indonesia & CNBC
            "div.post-content",         # Antara News
            "div.read__content",        # Kompas
            "div[class*='detail__body']", # Detikcom
            "div.artikel-isi",          # Republika
            "div.text-detail",          # Tempo
            "div.content-detail",       # Sindonews
            "article",
            "main"
        ]

        for selector in selectors:
            candidate = soup.select_one(selector)
            if candidate:
                content_container = candidate
                break

        if not content_container:
            content_container = soup.body or soup

        # Ambil semua paragraf
        paragraphs = []
        for p in content_container.find_all("p"):
            text = p.get_text().strip()
            # Filter paragraf sampah / iklan / navigasi
            if len(text) < 25:
                continue
            if any(junk in text.lower() for junk in ["baca juga:", "simak berita:", "pilihan editor:", "copyright", "hak cipta", "saksikan video"]):
                continue
            paragraphs.append(f"<p>{text}</p>")

        html_content = "".join(paragraphs)
        og_image = extract_image_from_entry(None, soup)

        return html_content, og_image

    except Exception:
        return "", None
