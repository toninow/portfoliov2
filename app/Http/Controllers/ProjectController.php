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
        $filters = $request->only(['q', 'categoria', 'estado', 'anio', 'tipo', 'tecnologia', 'orden']);

        $with = ['category', 'technologies', 'metrics' => fn ($q) => $q->where('is_public', true)];

        $applyFilters = function ($query) use ($request, $locale) {
            if ($search = trim((string) $request->query('q', ''))) {
                $query->where(function ($q) use ($search, $locale) {
                    $q->where('name->'.$locale, 'like', "%{$search}%")
                        ->orWhere('summary->'.$locale, 'like', "%{$search}%")
                        ->orWhere('outcome_headline->'.$locale, 'like', "%{$search}%");
                });
            }

            if ($category = $request->query('categoria')) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            }

            if ($lifecycle = $request->query('estado')) {
                $query->where('lifecycle', $lifecycle);
            }

            if ($year = $request->query('anio')) {
                $query->where('year', (int) $year);
            }

            if ($tech = $request->query('tecnologia')) {
                $query->whereHas('technologies', fn ($q) => $q->where('slug', $tech));
            }

            return $query;
        };

        $tipo = $request->query('tipo');
        $orden = $request->query('orden', 'relevancia');

        $sort = function ($query) use ($orden) {
            return match ($orden) {
                'fecha' => $query->orderByDesc('year')->orderBy('sort'),
                'destacados' => $query->orderByDesc('is_featured')->orderBy('sort')->orderByDesc('year'),
                default => $query->orderBy('sort')->orderByDesc('is_featured')->orderByDesc('year'),
            };
        };

        $caseQuery = $sort($applyFilters(Project::published()->with($with)->caseStudies()));
        $archiveQuery = $sort($applyFilters(Project::published()->with($with)->archive()));

        $caseStudies = collect();
        $archive = null;

        if ($tipo === 'archivo') {
            $archive = $archiveQuery->paginate(12)->withQueryString();
        } elseif ($tipo === 'caso') {
            $caseStudies = $caseQuery->get();
        } else {
            $caseStudies = $caseQuery->get();
            $archive = $archiveQuery->paginate(9)->withQueryString();
        }

        $totalVisible = $caseStudies->count() + ($archive?->total() ?? 0);

        return view('pages.projects.index', [
            'caseStudies' => $caseStudies,
            'archive' => $archive,
            'categories' => ProjectCategory::orderBy('sort')->get(),
            'technologies' => Technology::query()
                ->visible()
                ->where('show_on_projects', true)
                ->where('area', '!=', 'tools')
                ->whereHas('projects', fn ($q) => $q->published())
                ->orderBy('name')
                ->get(),
            'years' => Project::published()->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year'),
            'filters' => $filters,
            'totalVisible' => $totalVisible,
            'hasActiveFilters' => (bool) array_filter($filters, fn ($v) => filled($v)),
        ]);
    }

    public function show(Project $project): View
    {
        if (! $project->isPubliclyVisible()) {
            throw new NotFoundHttpException;
        }

        $project->load([
            'category',
            'technologies',
            'images' => fn ($q) => $q->where('is_visible', true),
            'metrics' => fn ($q) => $q->where('is_public', true),
        ]);

        $siblings = Project::published()
            ->when($project->is_case_study, fn ($q) => $q->caseStudies(), fn ($q) => $q->archive())
            ->orderBy('sort')
            ->orderByDesc('year')
            ->get(['id', 'slug', 'name']);

        $index = $siblings->search(fn ($p) => $p->id === $project->id);

        $techIds = $project->technologies->pluck('id');

        $related = Project::published()
            ->with(['category', 'technologies'])
            ->where('id', '!=', $project->id)
            ->where(function ($q) use ($project, $techIds) {
                $q->where('project_category_id', $project->project_category_id);
                if ($techIds->isNotEmpty()) {
                    $q->orWhereHas('technologies', fn ($t) => $t->whereIn('technologies.id', $techIds));
                }
            })
            ->orderByDesc('is_case_study')
            ->orderBy('sort')
            ->limit(3)
            ->get();

        return view('pages.projects.show', [
            'project' => $project,
            'previous' => $index !== false ? $siblings->get($index - 1) : null,
            'next' => $index !== false ? $siblings->get($index + 1) : null,
            'related' => $related,
        ]);
    }
}
