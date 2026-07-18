<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Antonio Benalcázar');
    }

    public function test_projects_index_loads_and_lists_published(): void
    {
        $this->get('/proyectos')->assertOk()->assertSee('MP Proveedores');
    }

    public function test_published_project_detail_loads(): void
    {
        $this->get('/proyectos/mp-proveedores')->assertOk()->assertSee('MP Proveedores');
    }

    public function test_draft_project_is_not_publicly_accessible(): void
    {
        $draft = Project::create([
            'slug' => 'borrador-oculto',
            'name' => ['es' => 'Borrador oculto', 'en' => 'Hidden draft'],
            'status' => 'draft',
            'visibility' => 'draft',
        ]);

        $this->get('/proyectos/'.$draft->slug)->assertNotFound();
    }

    public function test_services_about_contact_load(): void
    {
        $this->get('/servicios')->assertOk();
        $this->get('/sobre-mi')->assertOk();
        $this->get('/contacto')->assertOk();
    }

    public function test_english_locale_switch(): void
    {
        $this->get('/en')->assertOk()->assertSee('Projects');
        $this->get('/en/proyectos')->assertOk();
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap:');
    }

    public function test_unknown_route_returns_404(): void
    {
        $this->get('/pagina-inexistente')->assertNotFound();
    }
}
