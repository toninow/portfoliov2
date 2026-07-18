<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderBy('sort')
            ->paginate(9);

        return view('pages.blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): View
    {
        if (! $post->isPubliclyVisible()) {
            throw new NotFoundHttpException;
        }

        $recent = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('pages.blog.show', [
            'post' => $post,
            'recent' => $recent,
        ]);
    }
}
