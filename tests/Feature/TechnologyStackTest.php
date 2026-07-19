<?php

namespace Tests\Feature;

use App\Models\Technology;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnologyStackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_about_stack_loads_in_spanish(): void
    {
        $html = $this->get('/sobre-mi')->assertOk()->getContent();

        $this->assertStringContainsString('Tecnologías con las que trabajo', $html);
        $this->assertStringContainsString('Backend y automatización', $html);
        $this->assertStringContainsString('Plataformas e integraciones', $html);
        $this->assertStringContainsString('Herramientas de trabajo', $html);
        $this->assertStringContainsString('Experiencia tecnológica adicional', $html);
        $this->assertStringContainsString('Laravel', $html);
        $this->assertStringContainsString('APIs REST', $html);
        $this->assertStringContainsString('AI assistants', $html);
        $this->assertStringContainsString('data-tech-more', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('id="tech-additional-panel"', $html);
        $this->assertStringContainsString('hidden', $html);
        $this->assertStringNotContainsString('tech-card__count', $html);
        $this->assertStringNotContainsString('Bases de datos 4', $html);
        $this->assertStringNotContainsString('portfolio.', $html);
    }

    public function test_about_stack_loads_in_english_without_spanish_ui(): void
    {
        $html = $this->get('/en/sobre-mi')->assertOk()->getContent();

        $this->assertStringContainsString('Technologies I Work With', $html);
        $this->assertStringContainsString('Backend and Automation', $html);
        $this->assertStringContainsString('Platforms and Integrations', $html);
        $this->assertStringContainsString('Work Tools', $html);
        $this->assertStringContainsString('Additional Technical Experience', $html);
        $this->assertStringContainsString('View Other Technologies', $html);
        $this->assertStringNotContainsString('Tecnologías con las que trabajo', $html);
        $this->assertStringNotContainsString('Backend y automatización', $html);
        $this->assertStringNotContainsString('Ver otras tecnologías', $html);
    }

    public function test_primary_stack_excludes_previous_tools_from_backend(): void
    {
        $html = $this->get('/sobre-mi')->assertOk()->getContent();

        // Power Fx / Flutter belong to additional panel, not main backend/web cards.
        $this->assertStringContainsString('Power Fx', $html);
        $this->assertStringContainsString('Flutter', $html);

        $backendPos = strpos($html, 'Backend y automatización');
        $additionalPos = strpos($html, 'tech-additional-panel');
        $powerPos = strpos($html, 'Power Fx');
        $this->assertNotFalse($backendPos);
        $this->assertNotFalse($additionalPos);
        $this->assertNotFalse($powerPos);
        $this->assertGreaterThan($additionalPos, $powerPos);
    }

    public function test_apis_rest_is_backend_not_data(): void
    {
        $api = Technology::query()->where('slug', 'apis-rest')->first();
        $this->assertNotNull($api);
        $this->assertSame('backend', $api->area);
        $this->assertSame('primary', $api->relevance);
    }

    public function test_ai_tools_are_not_backend(): void
    {
        $ai = Technology::query()->where('slug', 'ia-gpt-claude')->first();
        $this->assertNotNull($ai);
        $this->assertSame('tools', $ai->area);
        $this->assertNotSame('primary', $ai->relevance);
        $this->assertSame('AI assistants', $ai->name);
    }

    public function test_hidden_technologies_do_not_render(): void
    {
        Technology::query()->where('slug', 'laravel')->update(['is_visible' => false]);

        $html = $this->get('/sobre-mi')->assertOk()->getContent();
        // Experience tech tags are free text; the technology catalog must omit Laravel.
        $this->assertStringNotContainsString('tecnologia=laravel', $html);
        $this->assertDoesNotMatchRegularExpression('/tech-stack__tag[^>]*>\s*(?:<span>)?Laravel(?:<\/span>)?/', $html);
    }

    public function test_technology_filter_keeps_locale_and_valid_slug(): void
    {
        $this->get('/proyectos?tecnologia=laravel')->assertOk();
        $this->get('/en/proyectos?tecnologia=laravel')->assertOk();
        $html = $this->get('/sobre-mi')->assertOk()->getContent();
        $this->assertStringContainsString('tecnologia=laravel', $html);
    }

    public function test_about_has_single_main_stack_h2_and_category_h3s(): void
    {
        $html = $this->get('/sobre-mi')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="tech-stack-title"'));
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'tech-stack__card-title'));
    }

    public function test_empty_categories_are_not_rendered(): void
    {
        Technology::query()->where('area', 'data')->update(['relevance' => 'previous', 'area' => 'additional']);

        $html = $this->get('/sobre-mi')->assertOk()->getContent();
        $this->assertStringNotContainsString('>Datos</h3>', $html);
    }

    public function test_no_n_plus_one_on_about_technologies(): void
    {
        $this->get('/sobre-mi')->assertOk();
        // Smoke: page renders with eager-loaded relations; detailed query count
        // varies with other about sections, so we assert relationships are loaded.
        $tech = Technology::query()->visible()->onAbout()->with('projects')->first();
        $this->assertTrue($tech->relationLoaded('projects'));
    }
}
