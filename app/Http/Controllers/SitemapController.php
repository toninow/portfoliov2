<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $static = [
            ['name' => 'home', 'priority' => '1.0'],
            ['name' => 'projects.index', 'priority' => '0.9'],
            ['name' => 'services.index', 'priority' => '0.8'],
            ['name' => 'blog.index', 'priority' => '0.8'],
            ['name' => 'about', 'priority' => '0.7'],
            ['name' => 'contact', 'priority' => '0.6'],
            ['name' => 'cv', 'priority' => '0.5'],
        ];

        $urls = [];

        foreach ($static as $page) {
            $urls[] = ['loc' => route($page['name']), 'priority' => $page['priority']];
            $urls[] = ['loc' => route('en.'.$page['name']), 'priority' => $page['priority']];
        }

        foreach (Project::published()->get() as $project) {
            $urls[] = [
                'loc' => route('projects.show', $project),
                'priority' => '0.7',
                'lastmod' => optional($project->updated_at)->toAtomString(),
            ];
            $urls[] = [
                'loc' => route('en.projects.show', $project),
                'priority' => '0.7',
                'lastmod' => optional($project->updated_at)->toAtomString(),
            ];
        }

        foreach (Post::published()->get() as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post),
                'priority' => '0.6',
                'lastmod' => optional($post->updated_at)->toAtomString(),
            ];
            $urls[] = [
                'loc' => route('en.blog.show', $post),
                'priority' => '0.6',
                'lastmod' => optional($post->updated_at)->toAtomString(),
            ];
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
