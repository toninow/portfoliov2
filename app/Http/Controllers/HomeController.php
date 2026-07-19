<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\HomepageSection;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillGroup;
use App\Models\Technology;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Project::published()->featured()
            ->with(['category', 'technologies', 'metrics' => fn ($q) => $q->where('is_public', true)])
            ->orderBy('sort')
            ->orderByDesc('year')
            ->get();

        $preferredCaseSlugs = [
            'mp-proveedores',
            'control-stock-dolibarr',
            'integracion-prestashop-dolibarr',
        ];

        $preferredCases = Project::published()->caseStudies()
            ->with(['category', 'technologies', 'metrics' => fn ($q) => $q->where('is_public', true)])
            ->whereIn('slug', $preferredCaseSlugs)
            ->get()
            ->sortBy(fn (Project $project) => array_search($project->slug, $preferredCaseSlugs, true))
            ->values();

        $homeCases = $preferredCases->count() >= 3
            ? $preferredCases->take(3)
            : Project::published()->caseStudies()
                ->with(['category', 'technologies', 'metrics' => fn ($q) => $q->where('is_public', true)])
                ->orderByDesc('is_featured')
                ->orderBy('sort')
                ->orderByDesc('year')
                ->take(3)
                ->get();

        $impactMetrics = $homeCases
            ->flatMap(fn ($p) => $p->metrics)
            ->unique(fn ($m) => $m->id)
            ->take(4)
            ->values();

        return view('pages.home', [
            'profile' => Profile::current(),
            'sections' => HomepageSection::visible()->get()->keyBy('key'),
            'homeCases' => $homeCases,
            'impactMetrics' => $impactMetrics,
            'featuredLarge' => $featured->where('featured_size', 'large')->take(1)->values(),
            'featuredMedium' => $featured->where('featured_size', 'medium')->take(2)->values(),
            'featuredCompact' => $featured->whereNotIn('featured_size', ['large', 'medium'])->take(3)->values(),
            'services' => Service::published()->with('technologies')->get(),
            'skillGroups' => SkillGroup::with('skills')->orderBy('sort')->get(),
            'technologies' => Technology::orderBy('sort')->get()->groupBy('area'),
            'experiences' => Experience::query()->visible()->ordered()->get(),
        ]);
    }
}
