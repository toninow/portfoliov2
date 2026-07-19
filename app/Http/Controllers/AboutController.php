<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\SkillGroup;
use App\Models\Technology;
use App\Support\TechnologyTaxonomy;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $technologies = Technology::query()
            ->visible()
            ->onAbout()
            ->ordered()
            ->with([
                'projects' => fn ($q) => $q->published()
                    ->orderByDesc('is_featured')
                    ->orderBy('sort')
                    ->select(['projects.id', 'projects.slug', 'projects.name', 'projects.is_featured', 'projects.sort', 'projects.status', 'projects.visibility']),
            ])
            ->withCount(['projects as published_projects_count' => fn ($q) => $q->published()])
            ->get();

        $stackGroups = collect(TechnologyTaxonomy::PRIMARY_STACK_AREAS)
            ->mapWithKeys(fn (string $area) => [
                $area => $technologies
                    ->where('area', $area)
                    ->where('relevance', 'primary')
                    ->values(),
            ])
            ->filter(fn ($group) => $group->isNotEmpty());

        $shownIds = $stackGroups->flatten(1)->pluck('id');

        $platforms = $technologies
            ->where('area', 'platforms')
            ->whereIn('relevance', ['primary', 'practical'])
            ->values();
        $shownIds = $shownIds->merge($platforms->pluck('id'));

        $tools = $technologies
            ->where('area', 'tools')
            ->values();
        $shownIds = $shownIds->merge($tools->pluck('id'));

        $additional = $technologies
            ->filter(fn (Technology $tech) => ! $shownIds->contains($tech->id))
            ->values();

        return view('pages.about', [
            'profile' => Profile::current(),
            'experiences' => Experience::query()->visible()->ordered()->get(),
            'education' => Education::orderBy('sort')->get(),
            'certifications' => Certification::orderBy('sort')->get(),
            'skillGroups' => SkillGroup::with('skills')->orderBy('sort')->get(),
            'stackGroups' => $stackGroups,
            'platformTechnologies' => $platforms,
            'toolTechnologies' => $tools,
            'additionalTechnologies' => $additional,
        ]);
    }
}
