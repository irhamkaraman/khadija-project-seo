import os
import uuid
import hashlib
import requests
from pathlib import Path
from typing import Optional
from scraper.config import STORAGE_POSTS_DIR, HTTP_HEADERS

def download_and_save_image(image_url: str) -> Optional[str]:
    """
    Mengunduh gambar dari URL dan menyimpannya di direktori storage/app/public/posts
    Persis seperti format upload Filament: 'posts/{filename}.jpg'
    """
    if not image_url or not image_url.startswith(("http://", "https://")):
        return None

    try:
        response = requests.get(
            image_url,
            headers=HTTP_HEADERS,
            timeout=15,
            stream=True
        )
        if response.status_code != 200:
            return image_url  # Simpan remote URL jika unduhan HTTP gagal

        content_type = response.headers.get("Content-Type", "").lower()
        
        # Tentukan ekstensi file
        ext = ".jpg"
        if "png" in content_type:
            ext = ".png"
        elif "webp" in content_type:
            ext = ".webp"
        elif "gif" in content_type:
            ext = ".gif"

        # Buat nama file unik (gaya Filament ULID/UUID)
        unique_token = uuid.uuid4().hex[:12]
        url_hash = hashlib.md5(image_url.encode("utf-8")).hexdigest()[:10]
        filename = f"{unique_token}_{url_hash}{ext}"
        
        file_path = STORAGE_POSTS_DIR / filename

        with open(file_path, "wb") as f:
            for chunk in response.iter_content(chunk_size=8192):
                if chunk:
                    f.write(chunk)

        # Pastikan file yang disimpan memiliki ukuran valid (> 1KB)
        if file_path.stat().st_size < 1024:
            file_path.unlink(missing_ok=True)
            return image_url

        # Format relatif persis sesuai Filament FileUpload: 'posts/{filename}'
        return f"posts/{filename}"

    except Exception as e:
        # Fallback ke direct URL jika terjadi error koneksi saat download
        return image_url
