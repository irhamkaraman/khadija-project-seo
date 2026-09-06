import json
import pymysql
import re
from datetime import datetime
from typing import Optional, List, Dict, Any
from scraper.config import DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

class DatabaseManager:
    """
    Mengelola koneksi dan operasi database MySQL langsung ke schema Laravel.
    Tabel: posts, categories, affiliate_links
    """

    def __init__(self):
        self.conn = None
        self.category_cache: Dict[str, int] = {}
        self.affiliate_links: List[str] = []
        self._connect()
        self._preload_categories()
        self._preload_affiliates()

    def _connect(self):
        try:
            self.conn = pymysql.connect(
                host=DB_HOST,
                port=DB_PORT,
                user=DB_USERNAME,
                password=DB_PASSWORD,
                database=DB_DATABASE,
                charset="utf8mb4",
                autocommit=True,
                cursorclass=pymysql.cursors.DictCursor
            )
        except Exception as e:
            raise ConnectionError(f"Gagal terhubung ke MySQL ({DB_HOST}:{DB_PORT}/{DB_DATABASE}): {e}")

    def _ensure_connected(self):
        try:
            self.conn.ping(reconnect=True)
        except Exception:
            self._connect()

    def _preload_categories(self):
        self._ensure_connected()
        with self.conn.cursor() as cur:
            cur.execute("SELECT id, name, slug FROM categories")
            rows = cur.fetchall()
            for r in rows:
                self.category_cache[r["slug"]] = r["id"]
                self.category_cache[r["name"].lower()] = r["id"]

    def _preload_affiliates(self):
        self._ensure_connected()
        try:
            with self.conn.cursor() as cur:
                cur.execute("SELECT affiliate_url FROM affiliate_links WHERE is_active = 1")
                rows = cur.fetchall()
                self.affiliate_links = [r["affiliate_url"] for r in rows if r.get("affiliate_url")]
        except Exception:
            self.affiliate_links = []

    def get_or_create_category(self, name: str, slug: Optional[str] = None) -> int:
        """
        Mendapatkan ID kategori atau membuat baru jika belum ada.
        """
        if not slug:
            slug = self._slugify(name)

        if slug in self.category_cache:
            return self.category_cache[slug]

        self._ensure_connected()
        now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        with self.conn.cursor() as cur:
            cur.execute("SELECT id FROM categories WHERE slug = %s LIMIT 1", (slug,))
            row = cur.fetchone()
            if row:
                cat_id = row["id"]
            else:
                cur.execute(
                    """
                    INSERT INTO categories (name, slug, created_at, updated_at)
                    VALUES (%s, %s, %s, %s)
                    """,
                    (name, slug, now, now)
                )
                cat_id = cur.lastrowid

            self.category_cache[slug] = cat_id
            self.category_cache[name.lower()] = cat_id
            return cat_id

    def post_exists(self, slug: str, title: str) -> bool:
        """
        Mengecek apakah postingan dengan slug atau judul yang sama sudah ada.
        """
        self._ensure_connected()
        with self.conn.cursor() as cur:
            cur.execute(
                "SELECT id FROM posts WHERE slug = %s OR title = %s LIMIT 1",
                (slug, title)
            )
            return cur.fetchone() is not None

    def make_unique_slug(self, base_slug: str) -> str:
        """
        Menghasilkan slug unik dengan memeriksa collision di tabel posts.
        """
        self._ensure_connected()
        slug = base_slug
        counter = 2
        with self.conn.cursor() as cur:
            while True:
                cur.execute("SELECT id FROM posts WHERE slug = %s LIMIT 1", (slug,))
                if not cur.fetchone():
                    return slug
                slug = f"{base_slug}-{counter}"
                counter += 1

    def insert_post(
        self,
        category_id: int,
        title: str,
        slug: str,
        content: str,
        image_url: Optional[str],
        share_links: Optional[List[Dict[str, str]]] = None,
        created_at: Optional[datetime] = None,
        updated_at: Optional[datetime] = None
    ) -> int:
        """
        Menyimpan berita baru ke tabel posts sesuai format Filament dan Laravel.
        """
        self._ensure_connected()

        now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        created_str = created_at.strftime("%Y-%m-%d %H:%M:%S") if created_at else now_str
        updated_str = updated_at.strftime("%Y-%m-%d %H:%M:%S") if updated_at else created_str

        # Jika share_links belum diset, ambil dari active affiliate links
        if not share_links and self.affiliate_links:
            import random
            selected_url = random.choice(self.affiliate_links)
            share_links = [{"url": selected_url}]

        share_links_json = json.dumps(share_links or [], ensure_ascii=False)

        with self.conn.cursor() as cur:
            sql = """
                INSERT INTO posts (
                    category_id,
                    title,
                    slug,
                    content,
                    image_url,
                    share_links,
                    created_at,
                    updated_at
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cur.execute(
                sql,
                (
                    category_id,
                    title,
                    slug,
                    content,
                    image_url,
                    share_links_json,
                    created_str,
                    updated_str
                )
            )
            return cur.lastrowid

    def get_stats(self) -> Dict[str, int]:
        self._ensure_connected()
        with self.conn.cursor() as cur:
            cur.execute("SELECT COUNT(*) AS total_posts FROM posts")
            total_posts = cur.fetchone()["total_posts"]

            cur.execute("SELECT COUNT(*) AS total_categories FROM categories")
            total_categories = cur.fetchone()["total_categories"]

            return {
                "total_posts": total_posts,
                "total_categories": total_categories
            }

    @staticmethod
    def _slugify(text: str) -> str:
        text = text.lower()
        text = re.sub(r"[^\w\s-]", "", text)
        text = re.sub(r"[\s_-]+", "-", text)
        return text.strip("-")

    def close(self):
        if self.conn:
            try:
                self.conn.close()
            except Exception:
                pass
