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
     * Otomatis kompresi ukuran < 300KB agar thumbnail WhatsApp tidak di-drop.
     */
    public function ogImage($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post || !$post->image_url) {
            $fallback = public_path('favicon.ico');
            if (file_exists($fallback)) {
                return response()->file($fallback, [
                    'Content-Type' => 'image/x-icon',
                    'Access-Control-Allow-Origin' => '*',
                ]);
            }
            abort(404);
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

        if (!file_exists($filePath) && $remoteUrl) {
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $downloaded = @file_get_contents($remoteUrl, false, stream_context_create([
                'http' => ['timeout' => 5],
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
            ]));
            if ($downloaded) {
                @file_put_contents($filePath, $downloaded);
            }
        }

        if (!file_exists($filePath)) {
            $fallback = public_path('favicon.ico');
            if (file_exists($fallback)) {
                return response()->file($fallback, [
                    'Content-Type' => 'image/x-icon',
                    'Access-Control-Allow-Origin' => '*',
                ]);
            }
            abort(404);
        }

        // Cache direktori untuk gambar OG terkompresi
        $cacheDir = storage_path('app/public/og-cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $cachedFile = $cacheDir . '/' . md5($post->slug . '_' . filemtime($filePath)) . '.jpg';

        if (!file_exists($cachedFile)) {
            $imgInfo = @getimagesize($filePath);
            if ($imgInfo && extension_loaded('gd')) {
                $srcImg = match ($imgInfo[2]) {
                    IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
                    IMAGETYPE_PNG => @imagecreatefrompng($filePath),
                    IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : null,
                    default => null,
                };

                if ($srcImg) {
                    $origWidth = imagesx($srcImg);
                    $origHeight = imagesy($srcImg);

                    $targetWidth = min(1200, $origWidth);
                    $targetHeight = (int) round(($origHeight / $origWidth) * $targetWidth);

                    $destImg = imagecreatetruecolor($targetWidth, $targetHeight);

                    // Beri latar belakang putih untuk PNG transparan
                    $white = imagecolorallocate($destImg, 255, 255, 255);
                    imagefilledrectangle($destImg, 0, 0, $targetWidth, $targetHeight, $white);

                    imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

                    // Simpan sebagai JPEG kualitas 82 (ukuran stabil 70KB - 160KB, ramah WhatsApp)
                    imagejpeg($destImg, $cachedFile, 82);

                    imagedestroy($srcImg);
                    imagedestroy($destImg);
                }
            }
        }

        $serveFile = file_exists($cachedFile) ? $cachedFile : $filePath;
        $mime = file_exists($cachedFile) ? 'image/jpeg' : (mime_content_type($serveFile) ?: 'image/jpeg');

        return response()->file($serveFile, [
            'Content-Type'                => $mime,
            'Content-Length'              => filesize($serveFile),
            'Cache-Control'               => 'public, max-age=2592000, immutable',
            'Access-Control-Allow-Origin' => '*',
            'Accept-Ranges'               => 'bytes',
        ]);
    }
}
