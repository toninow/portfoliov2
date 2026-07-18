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
            ->with(['category', 'technologies'])
            ->orderBy('sort')
            ->orderByDesc('year')
            ->get();

        return view('pages.home', [
            'profile' => Profile::current(),
            'sections' => HomepageSection::visible()->get()->keyBy('key'),
            'featuredLarge' => $featured->where('featured_size', 'large')->take(1)->values(),
            'featuredMedium' => $featured->where('featured_size', 'medium')->take(2)->values(),
            'featuredCompact' => $featured->whereNotIn('featured_size', ['large', 'medium'])->take(3)->values(),
            'services' => Service::published()->with('technologies')->get(),
            'skillGroups' => SkillGroup::with('skills')->orderBy('sort')->get(),
            'technologies' => Technology::orderBy('sort')->get()->groupBy('area'),
            'experiences' => Experience::orderBy('sort')->get(),
        ]);
    }
}
