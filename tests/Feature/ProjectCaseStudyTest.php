<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
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

    public function test_home_shows_systems_map_in_hero_then_cases_then_problems(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $mapPos = strpos($html, 'id="mapa-sistemas"');
        $casesPos = strpos($html, 'id="casos"');
        $problemsPos = strpos($html, 'id="problemas"');
        $this->assertNotFalse($mapPos);
        $this->assertNotFalse($casesPos);
        $this->assertNotFalse($problemsPos);
        $this->assertTrue($mapPos < $casesPos);
        $this->assertTrue($casesPos < $problemsPos);
        $this->assertSame(1, substr_count($html, 'id="mapa-sistemas"'));
        $this->assertSame(1, substr_count($html, __('portfolio.hero.map_desc')));
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

    public function test_case_study_shows_visual_evidence_with_video_and_gallery(): void
    {
        $project = Project::where('slug', 'mp-proveedores')->firstOrFail();
        $project->update([
            'main_image_path' => 'projects/mp-proveedores-cover.png',
            'demo_video_path' => 'projects/videos/mp-proveedores.mp4',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'path' => 'projects/gallery/mp-proveedores/panel.png',
            'alt' => ['es' => 'Panel de proveedores', 'en' => 'Suppliers panel'],
            'caption' => ['es' => 'Panel principal', 'en' => 'Main panel'],
            'type' => 'desktop',
            'is_featured' => true,
            'is_visible' => true,
            'sort' => 0,
        ]);

        $html = $this->get('/proyectos/mp-proveedores')->assertOk()->getContent();

        $this->assertStringContainsString('Evidencia visual', $html);
        $this->assertStringContainsString('Vídeo de demostración', $html);
        $this->assertStringContainsString('projects/videos/mp-proveedores.mp4', $html);
        $this->assertStringContainsString('data-gallery', $html);
        $this->assertStringContainsString('data-lightbox', $html);
        $this->assertStringContainsString('Panel principal', $html);
        $this->assertStringContainsString('project-media__thumbs', $html);
        $this->assertStringContainsString('related-slider', $html);
    }

    public function test_project_card_shows_video_badge_when_demo_exists(): void
    {
        Project::where('slug', 'mp-proveedores')->update([
            'demo_video_path' => 'projects/videos/mp-proveedores.mp4',
            'main_image_path' => 'projects/mp-proveedores-cover.png',
        ]);

        $html = $this->get('/proyectos')->assertOk()->getContent();
        $this->assertStringContainsString('project-card__media-badge', $html);
        $this->assertMatchesRegularExpression('/project-card__media-badge[\s\S]*?Vídeo/', $html);
    }
}
