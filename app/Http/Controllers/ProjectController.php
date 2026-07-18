<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        $query = Project::published()->with(['category', 'technologies']);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search, $locale) {
                $q->where('name->'.$locale, 'like', "%{$search}%")
                    ->orWhere('summary->'.$locale, 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('categoria')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if ($tech = $request->query('tecnologia')) {
            $query->whereHas('technologies', fn ($q) => $q->where('slug', $tech));
        }

        if ($year = $request->query('anio')) {
            $query->where('year', (int) $year);
        }

        $projects = $query->orderByDesc('is_featured')
            ->orderBy('sort')
            ->orderByDesc('year')
            ->paginate(9)
            ->withQueryString();

        return view('pages.projects.index', [
            'projects' => $projects,
            'categories' => ProjectCategory::orderBy('sort')->get(),
            'technologies' => Technology::orderBy('name')->get(),
            'years' => Project::published()->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year'),
            'filters' => $request->only(['q', 'categoria', 'tecnologia', 'anio']),
        ]);
    }

    public function show(Project $project): View
    {
        if (! $project->isPubliclyVisible()) {
            throw new NotFoundHttpException;
        }

        $project->load(['category', 'technologies', 'images', 'metrics' => fn ($q) => $q->where('is_public', true)]);

        $siblings = Project::published()->orderBy('sort')->orderByDesc('year')->get(['id', 'slug', 'name']);
        $index = $siblings->search(fn ($p) => $p->id === $project->id);

        return view('pages.projects.show', [
            'project' => $project,
            'previous' => $index !== false ? $siblings->get($index - 1) : null,
            'next' => $index !== false ? $siblings->get($index + 1) : null,
            'related' => Project::published()
                ->where('project_category_id', $project->project_category_id)
                ->where('id', '!=', $project->id)
                ->limit(3)->get(),
        ]);
    }
}
