<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('projects.index'), 'priority' => '0.9'],
            ['loc' => route('services.index'), 'priority' => '0.8'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('contact'), 'priority' => '0.6'],
        ];

        foreach (Project::published()->get() as $project) {
            $urls[] = [
                'loc' => route('projects.show', $project),
                'priority' => '0.7',
                'lastmod' => optional($project->updated_at)->toAtomString(),
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
