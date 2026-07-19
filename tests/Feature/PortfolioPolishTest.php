<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioPolishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_home_shows_cases_before_systems_map(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $casesPos = strpos($html, 'id="casos"');
        $mapPos = strpos($html, 'id="mapa-sistemas"');

        $this->assertNotFalse($casesPos);
        $this->assertNotFalse($mapPos);
        $this->assertLessThan($mapPos, $casesPos);
        $this->assertStringContainsString(__('portfolio.projects.view_all_cases'), $html);
    }

    public function test_services_page_publishes_four_canonical_services_without_ai_duplicate(): void
    {
        $this->assertSame(4, Service::published()->count());

        $response = $this->get('/servicios')->assertOk();
        $html = $response->getContent();

        $response->assertSee('Aplicaciones internas y automatización');
        $response->assertSee('Integraciones empresariales');
        $response->assertSee('Datos y catálogos');
        $response->assertSee('Infraestructura y continuidad');
        $response->assertDontSee('Automatización de procesos con IA');
        $this->assertStringContainsString(__('portfolio.services.page_eyebrow'), $html);
        $this->assertEquals(1, substr_count($html, '<h1'));
    }

    public function test_projects_page_does_not_duplicate_heading_text(): void
    {
        $html = $this->get('/proyectos')->assertOk()->getContent();

        $this->assertStringContainsString(__('portfolio.projects.page_eyebrow'), $html);
        $this->assertStringContainsString('<h1', $html);
        $this->assertEquals(1, substr_count($html, '<h1'));
    }

    public function test_contact_email_is_not_double_at_prefixed(): void
    {
        $html = $this->get('/contacto')->assertOk()->getContent();

        $this->assertStringNotContainsString('@contacto@', $html);
        $this->assertStringContainsString('contacto@antoniobc.net', $html);
        $this->assertStringContainsString(__('portfolio.contact.reason'), $html);
    }

    public function test_footer_keeps_admin_link_and_auth_label_changes(): void
    {
        $guest = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('href="'.url('/admin').'"', $guest);
        $this->assertStringContainsString(__('portfolio.footer.admin'), $guest);

        $user = User::factory()->create(['role' => 'super_admin']);
        $auth = $this->actingAs($user)->get('/')->assertOk()->getContent();
        $this->assertStringContainsString(__('portfolio.footer.admin_logged_in'), $auth);
    }

    public function test_admin_login_is_noindex(): void
    {
        $html = $this->get('/admin/login')->assertOk()->getContent();
        $this->assertStringContainsString('name="robots"', $html);
        $this->assertStringContainsString('noindex', $html);
    }

    public function test_sitemap_includes_english_urls(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('en.home'), $xml);
        $this->assertStringContainsString(route('en.services.index'), $xml);
        $this->assertStringContainsString(route('en.cv'), $xml);
    }

    public function test_english_services_page_is_translated(): void
    {
        $this->get('/en/servicios')
            ->assertOk()
            ->assertSee('Internal applications and automation')
            ->assertSee('Business integrations')
            ->assertDontSee('Automatización de procesos con IA');
    }
}
