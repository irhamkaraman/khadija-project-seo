<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteRedirectController extends Controller
{
    public function __invoke($slug)
    {
        $site = \App\Models\Site::where('slug', $slug)->firstOrFail();
        
        return view('redirect', compact('site'));
    }
}
