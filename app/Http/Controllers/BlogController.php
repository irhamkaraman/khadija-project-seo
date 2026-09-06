<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function home()
    {
        $latestPosts  = Post::with('category')->latest()->take(5)->get();
        $morePosts    = Post::with('category')->latest()->skip(5)->take(6)->get();
        $categories   = Category::withCount('posts')->get();
        $totalPosts   = Post::count();

        $affiliates   = AffiliateLink::active()->inRandomOrder()->limit(12)->get();
        $floatingAds  = AffiliateLink::active()->inRandomOrder()->limit(2)->get();

        return view('home', compact('latestPosts', 'morePosts', 'categories', 'totalPosts', 'affiliates', 'floatingAds'));
    }

    public function index()
    {
        $posts = Post::latest()->paginate(12);
        $categories = Category::all();
        
        return view('blog.index', compact('posts', 'categories'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->latest()->paginate(12);
        $categories = Category::all();
        
        return view('blog.category', compact('category', 'posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // Pra-kompresi / generate gambar OG di awal agar saat link dibagikan ke WhatsApp, gambar sudah siap saji (< 10ms)
        try {
            self::resolveOgImagePath($post);
        } catch (\Throwable $e) {
            // Silently continue jika terjadi kendala pada gambar
        }
        
        $randomShareLink = null;
        if (!empty($post->share_links)) {
            $links = collect($post->share_links)->pluck('url')->filter()->toArray();
            if (count($links) > 0) {
                $randomShareLink = $links[array_rand($links)];
            }
        }
        
        return view('blog.show', compact('post', 'randomShareLink'));
    }

    public function ajaxAds(Request $request)
    {
        $limit = $request->get('limit', 5);
        $ads = AffiliateLink::active()
            ->whereNotNull('image_url')
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($ad) {
                return [
                    'title' => $ad->title,
                    'image_url' => \Illuminate\Support\Str::startsWith($ad->image_url, 'http') ? $ad->image_url : url('/file/' . $ad->image_url),
                    'go_url' => route('affiliate.go', $ad->slug)
                ];
            });

        return response()->json($ads);
    }

    /**
     * Menyajikan gambar Open Graph yang dioptimasi khusus untuk crawler sosial (WhatsApp, FB, IG, Twitter).
     * Otomatis kompresi ukuran < 300KB agar thumbnail WhatsApp dijamin tampil.
     */
    public function ogImage($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post || empty($post->image_url)) {
            return $this->serveFallbackIcon();
        }

        $serveFile = self::resolveOgImagePath($post);

        if ($serveFile && file_exists($serveFile)) {
            $mime = str_ends_with($serveFile, '.jpg') || str_ends_with($serveFile, '.jpeg') ? 'image/jpeg' : (mime_content_type($serveFile) ?: 'image/jpeg');
            return response()->file($serveFile, [
                'Content-Type'                => $mime,
                'Content-Length'              => filesize($serveFile),
                'Cache-Control'               => 'public, max-age=31536000, immutable',
                'Access-Control-Allow-Origin' => '*',
                'Accept-Ranges'               => 'bytes',
            ]);
        }

        // Jika gambar eksternal dan belum berhasil didownload, redirect langsung ke CDN asli
        if (\Illuminate\Support\Str::startsWith($post->image_url, ['http://', 'https://'])) {
            return redirect()->away($post->image_url);
        }

        return $this->serveFallbackIcon();
    }

    /**
     * Memastikan file OG gambar terkompresi (< 300KB) sudah tersedia di disk
     */
    public static function resolveOgImagePath(Post $post): ?string
    {
        if (empty($post->image_url)) {
            return null;
        }

        $cacheDir = storage_path('app/public/og-cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $isExternal = \Illuminate\Support\Str::startsWith($post->image_url, ['http://', 'https://']);
        if ($isExternal) {
            $remoteUrl = $post->image_url;
            $ext = pathinfo(parse_url($remoteUrl, PHP_URL_PATH) ?: 'image.jpg', PATHINFO_EXTENSION) ?: 'jpg';
            $filePath = storage_path('app/public/posts/' . md5($remoteUrl) . '.' . $ext);
        } else {
            $rawImage = ltrim($post->image_url, '/');
            $rawImage = preg_replace('#^(file/|storage/)#', '', $rawImage);
            $filePath = storage_path('app/public/' . $rawImage);
            $remoteUrl = null;

            if (!file_exists($filePath)) {
                $appUrl = config('app.url');
                if (!empty($appUrl) && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
                    $remoteUrl = rtrim($appUrl, '/') . '/file/' . $rawImage;
                }
            }
        }

        // Unduh dari remote jika belum tersimpan lokal
        if (!file_exists($filePath) && $remoteUrl) {
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $downloaded = @file_get_contents($remoteUrl, false, stream_context_create([
                'http' => [
                    'timeout' => 4,
                    'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\nAccept: image/*,*/*;q=0.8\r\n",
                ],
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
            ]));
            if ($downloaded) {
                @file_put_contents($filePath, $downloaded);
            }
        }

        if (!file_exists($filePath)) {
            return null;
        }

        // Buat file kompresi khusus WhatsApp (target: 1200x630 atau proporsional, kualitas 82, size < 250KB)
        $cachedFile = $cacheDir . '/' . md5($post->slug . '_' . filemtime($filePath)) . '.jpg';

        if (file_exists($cachedFile)) {
            return $cachedFile;
        }

        $imgInfo = @getimagesize($filePath);
        if ($imgInfo && extension_loaded('gd')) {
            $srcImg = match ($imgInfo[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
                IMAGETYPE_PNG  => @imagecreatefrompng($filePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : null,
                default        => null,
            };

            if ($srcImg) {
                $origWidth  = imagesx($srcImg);
                $origHeight = imagesy($srcImg);

                // Standar emas Facebook / WhatsApp: 1200x630
                $targetWidth  = min(1200, max(400, $origWidth));
                $targetHeight = (int) round(($origHeight / $origWidth) * $targetWidth);

                $destImg = imagecreatetruecolor($targetWidth, $targetHeight);

                // Latar belakang putih untuk PNG transparan
                $white = imagecolorallocate($destImg, 255, 255, 255);
                imagefilledrectangle($destImg, 0, 0, $targetWidth, $targetHeight, $white);

                imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

                // Simpan sebagai JPEG kualitas 82 (ukuran stabil 60KB - 180KB, ramah WhatsApp crawler)
                imagejpeg($destImg, $cachedFile, 82);

                imagedestroy($srcImg);
                imagedestroy($destImg);

                return $cachedFile;
            }
        }

        return $filePath;
    }

    protected function serveFallbackIcon()
    {
        $fallback = public_path('favicon.ico');
        if (file_exists($fallback)) {
            return response()->file($fallback, [
                'Content-Type' => 'image/x-icon',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }
        abort(404);
    }
}
