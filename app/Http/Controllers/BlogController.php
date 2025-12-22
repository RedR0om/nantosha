<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('is_published', true)
            ->where('published_at', '<=', now())
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->with('author')
            ->firstOrFail();

        // Increment views
        $post->increment('views');

        // Related posts
        $relatedPosts = BlogPost::where('id', '!=', $post->id)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->limit(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
