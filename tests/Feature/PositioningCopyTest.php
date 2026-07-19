<?php

namespace Tests\Feature;

use App\Models\Profile;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositioningCopyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_home_loads_in_spanish_with_positioning(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertStringContainsString('Backend, automatización e integraciones', $html);
        $this->assertStringContainsString('Conecto sistemas y convierto procesos manuales', $html);
        $this->assertStringContainsString('Ver casos de estudio', $html);
        $this->assertStringContainsString('Problemas que ayudo a resolver', $html);
        $this->assertStringContainsString('href="#casos"', $html);
        $this->assertStringNotContainsString('Automatización + IA', $html);
        $this->assertStringNotContainsString('portfolio.hero.', $html);
        $this->assertStringNotContainsString('portfolio.cta.', $html);
        $this->assertStringContainsString(
            'Antonio Benalcázar | Backend, automatización e integraciones',
            $html
        );
    }

    public function test_home_loads_in_english_without_spanish_ui_strings(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertStringContainsString('Backend, Automation and Integrations', $html);
        $this->assertStringContainsString('View Case Studies', $html);
        $this->assertStringContainsString('Problems I Help Solve', $html);
        $this->assertStringContainsString(
            'Antonio Benalcázar | Backend, Automation and Integrations',
            $html
        );
        $this->assertStringNotContainsString('Ver casos de estudio', $html);
        $this->assertStringNotContainsString('Problemas que ayudo a resolver', $html);
        $this->assertStringNotContainsString('portfolio.hero.', $html);
    }

    public function test_about_page_loads_in_both_locales(): void
    {
        $es = $this->get('/sobre-mi')->assertOk()->getContent();
        $en = $this->get('/en/sobre-mi')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/i', $es));
        $this->assertSame(1, preg_match_all('/<h1\b/i', $en));
        $this->assertStringContainsString('Mi evolución profesional', $es);
        $this->assertStringContainsString('My professional path', $en);
        $this->assertStringContainsString('Uso responsable de inteligencia artificial', $es);
        $this->assertStringContainsString('Responsible use of AI', $en);
        $this->assertStringContainsString('Ver proyectos', $es);
        $this->assertStringContainsString('View Projects', $en);
        $this->assertStringNotContainsString('Mi evolución profesional', $en);
    }

    public function test_cta_links_are_valid(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="'.url('/contacto').'"', $html);
        $this->assertStringContainsString('href="'.url('/sobre-mi').'"', $html);
        $this->assertStringContainsString('Ver perfil profesional', $html);
    }

    public function test_home_falls_back_when_profile_copy_is_empty(): void
    {
        $profile = Profile::current();
        foreach (['es', 'en'] as $locale) {
            $profile->setTranslation('headline', $locale, '');
            $profile->setTranslation('bio', $locale, '');
            $profile->setTranslation('availability', $locale, '');
        }
        $profile->save();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Desarrollador de software · Backend, automatización e integraciones', $html);
        $this->assertStringContainsString('Desarrollo aplicaciones internas, automatizaciones e integraciones', $html);
        $this->assertStringContainsString('Disponible para oportunidades profesionales y proyectos seleccionados.', $html);
    }

    public function test_experience_role_describes_internal_systems_work(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Informático · Desarrollo de software y sistemas internos', $html);
        $this->assertStringContainsString('integraciones entre Dolibarr y PrestaShop', $html);
    }

    public function test_english_metadata_is_locale_specific(): void
    {
        $es = $this->get('/')->assertOk()->getContent();
        $en = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString(
            'content="Desarrollador de software especializado en backend',
            $es
        );
        $this->assertStringContainsString(
            'content="Software developer specialised in backend systems',
            $en
        );
        $this->assertStringContainsString('"@type":"WebSite"', $es);
        $this->assertStringContainsString('"@type":"Person"', $es);
    }
}
