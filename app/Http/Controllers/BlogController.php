<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = \App\Models\Post::latest()->paginate(12);
        $categories = \App\Models\Category::all();
        
        return view('blog.index', compact('posts', 'categories'));
    }

    public function category($slug)
    {
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->latest()->paginate(12);
        $categories = \App\Models\Category::all();
        
        return view('blog.category', compact('category', 'posts', 'categories'));
    }

    public function show($slug)
    {
        $post = \App\Models\Post::where('slug', $slug)->firstOrFail();
        
        // Pilih satu share link secara acak dari array share_links
        $randomShareLink = null;
        if (!empty($post->share_links)) {
            // Ambil array of URLs (format dari Repeater adalah array dari array: [['url' => '...'], ['url' => '...']])
            $links = collect($post->share_links)->pluck('url')->filter()->toArray();
            if (count($links) > 0) {
                $randomShareLink = $links[array_rand($links)];
            }
        }
        
        return view('blog.show', compact('post', 'randomShareLink'));
    }
}
