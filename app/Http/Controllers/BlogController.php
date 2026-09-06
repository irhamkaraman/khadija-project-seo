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
}
