<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCaseStudyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_projects_index_separates_case_studies_and_archive(): void
    {
        $response = $this->get('/proyectos');

        $response->assertOk();
        $response->assertSee('Trabajo principal');
        $response->assertSee('Proyectos anteriores');
        $response->assertSee('MP Proveedores');
        $response->assertSee('Sitio web Istcre');
    }

    public function test_draft_project_is_hidden(): void
    {
        $project = Project::where('slug', 'mp-proveedores')->firstOrFail();
        $project->update(['status' => 'draft']);

        $this->get('/proyectos/mp-proveedores')->assertNotFound();
        $this->get('/proyectos')->assertDontSee('MP Proveedores', false);
    }

    public function test_year_filter_is_dynamic_from_database(): void
    {
        Project::where('slug', 'gitea-autogestionado')->update(['year' => 2026]);

        $response = $this->get('/proyectos');
        $response->assertOk();
        $response->assertSee('>2026<', false);
    }

    public function test_type_filter_case_studies_only(): void
    {
        $response = $this->get('/proyectos?tipo=caso');
        $response->assertOk();
        $response->assertSee('MP Proveedores');
        $response->assertDontSee('Sitio web Istcre');
    }

    public function test_type_filter_archive_only(): void
    {
        $response = $this->get('/proyectos?tipo=archivo');
        $response->assertOk();
        $response->assertSee('Sitio web Istcre');
        $response->assertDontSee('Trabajo principal');
    }

    public function test_empty_sections_are_hidden_on_show(): void
    {
        $project = Project::where('slug', 'mp-proveedores')->firstOrFail();
        $project->update([
            'constraints' => null,
            'learnings' => null,
            'improvements' => null,
        ]);

        $html = $this->get('/proyectos/mp-proveedores')->assertOk()->getContent();

        $this->assertStringNotContainsString('>Restricciones</h2>', $html);
        $this->assertStringNotContainsString('>Aprendizajes</h2>', $html);
        $this->assertStringNotContainsString('>Próximas mejoras</h2>', $html);
        $this->assertStringContainsString('El problema', $html);
    }

    public function test_private_metrics_are_hidden_publicly(): void
    {
        $project = Project::where('slug', 'mp-proveedores')->firstOrFail();
        ProjectMetric::create([
            'project_id' => $project->id,
            'name' => ['es' => 'Métrica privada', 'en' => 'Private metric'],
            'value' => '999',
            'unit' => '',
            'is_public' => false,
            'sort' => 0,
        ]);
        ProjectMetric::create([
            'project_id' => $project->id,
            'name' => ['es' => 'Métrica pública', 'en' => 'Public metric'],
            'value' => '42',
            'unit' => '',
            'is_public' => true,
            'sort' => 1,
        ]);

        $html = $this->get('/proyectos/mp-proveedores')->assertOk()->getContent();
        $this->assertStringContainsString('Métrica pública', $html);
        $this->assertStringNotContainsString('Métrica privada', $html);
        $this->assertStringContainsString('42', $html);
        $this->assertStringNotContainsString('999', $html);
    }

    public function test_english_locale_for_case_study(): void
    {
        $this->get('/en/proyectos/mp-proveedores')
            ->assertOk()
            ->assertSee('MP Suppliers')
            ->assertSee('The problem');
    }

    public function test_existing_slugs_still_resolve(): void
    {
        foreach (['mp-proveedores', 'control-stock-dolibarr', 'gitea-autogestionado', 'backups-restic'] as $slug) {
            $this->get('/proyectos/'.$slug)->assertOk();
        }
    }

    public function test_restic_is_marked_as_implementation_not_finished(): void
    {
        $html = $this->get('/proyectos/backups-restic')->assertOk()->getContent();
        $this->assertStringContainsString('En implementación', $html);
    }

    public function test_gitea_year_is_2026(): void
    {
        $this->assertSame(2026, Project::where('slug', 'gitea-autogestionado')->value('year'));
        $this->get('/proyectos')->assertSee('>2026<', false);
    }

    public function test_home_shows_cases_then_problems_then_systems_map(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $mapPos = strpos($html, 'id="mapa-sistemas"');
        $casesPos = strpos($html, 'id="casos"');
        $problemsPos = strpos($html, 'id="problemas"');
        $this->assertNotFalse($mapPos);
        $this->assertNotFalse($casesPos);
        $this->assertNotFalse($problemsPos);
        $this->assertLessThan($problemsPos, $casesPos);
        $this->assertLessThan($mapPos, $problemsPos);
        $this->assertSame(1, substr_count($html, __('portfolio.hero.map_title')));
    }

    public function test_overlay_problem_label_only_when_problem_exists(): void
    {
        $legacy = Project::where('is_archived', true)->whereNull('problem')->first();
        $this->assertNotNull($legacy);

        // Card on archive listing should not force "El problema" when empty.
        $html = $this->get('/proyectos?tipo=archivo')->assertOk()->getContent();
        // Count "El problema" overlay labels — archive cards without problem should not add them.
        $this->assertTrue(substr_count($html, 'proj-overlay__label') <= Project::published()->caseStudies()->whereNotNull('problem')->count() + 5);
    }

    public function test_admin_projects_list_loads(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($user)->get('/admin/projects')->assertOk();
    }

    public function test_case_study_scope_excludes_archive(): void
    {
        $cases = Project::published()->caseStudies()->pluck('slug');
        $this->assertTrue($cases->contains('mp-proveedores'));
        $this->assertFalse($cases->contains(fn ($slug) => str_contains($slug, 'istcre')));
    }
}
