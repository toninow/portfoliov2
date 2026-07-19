<?php

namespace Tests\Feature;

use App\Models\Experience;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_experience_section_loads_in_spanish(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Trayectoria profesional', $html);
        $this->assertStringContainsString('id="experiencia-title"', $html);
        $this->assertSame(1, substr_count($html, 'id="experiencia-title"'));
        $this->assertStringContainsString('Una trayectoria que combina desarrollo de software', $html);
        $this->assertStringContainsString('Informático · Desarrollo de software y sistemas internos', $html);
        $this->assertStringContainsString('Musical Princesa', $html);
        $this->assertStringContainsString('Madrid, España', $html);
        $this->assertStringContainsString('2025 – Actualidad', $html);
        $this->assertStringContainsString('Puesto actual', $html);
        $this->assertStringNotContainsString('TICS', $html);
        $this->assertStringContainsString('Soporte técnico TIC', $html);
        $this->assertStringContainsString('Becario de desarrollo de software', $html);
        $this->assertStringContainsString('Cofundador y desarrollador web', $html);
        $this->assertStringContainsString('Proyecto paralelo', $html);
        $this->assertStringContainsString('Ver trayectoria completa', $html);
        $this->assertStringNotContainsString('portfolio.', $html);
    }

    public function test_experience_section_loads_in_english_without_spanish_fragments(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString('Professional Background', $html);
        $this->assertStringContainsString('Experience', $html);
        $this->assertStringContainsString('IT Specialist · Software Development and Internal Systems', $html);
        $this->assertStringContainsString('Software Development Intern', $html);
        $this->assertStringContainsString('Side project', $html);
        $this->assertStringContainsString('Current role', $html);
        $this->assertStringContainsString('2025 – Present', $html);
        $this->assertStringContainsString('Madrid, Spain', $html);
        $this->assertStringContainsString('Digital Marketing', $html);
        $this->assertStringNotContainsString('Trayectoria profesional', $html);
        $this->assertStringNotContainsString('Actualidad', $html);
        $this->assertStringNotContainsString('TICS', $html);
        $this->assertStringNotContainsString('Informático', $html);
        $this->assertStringNotContainsString('Marketing digital', $html);
    }

    public function test_roles_appear_in_reverse_chronological_order(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $companies = ['Musical Princesa', 'R&amp;B Studio', 'Cruz Roja', 'Algoritmun'];
        $positions = array_map(fn ($name) => strpos($html, $name), $companies);

        foreach ($positions as $pos) {
            $this->assertNotFalse($pos);
        }

        $this->assertTrue($positions[0] < $positions[1]);
        $this->assertTrue($positions[1] < $positions[2]);
        $this->assertTrue($positions[2] < $positions[3]);
    }

    public function test_desktop_sides_alternate_from_index_without_duplicating_items(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match_all('/timeline__item--(left|right)/', $html, $matches);
        $this->assertSame(['left', 'right', 'left', 'right'], $matches[1]);
        $this->assertSame(4, substr_count($html, 'timeline__role'));
        $this->assertSame(4, substr_count($html, '<article class="timeline__card">'));
    }

    public function test_hidden_experiences_are_not_rendered(): void
    {
        Experience::query()->where('company', 'Algoritmun')->update(['is_visible' => false]);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Algoritmun', $html);
        $this->assertStringContainsString('Musical Princesa', $html);
    }

    public function test_current_role_has_no_end_date_and_no_redundant_actual_badge(): void
    {
        $current = Experience::query()->where('is_current', true)->first();
        $this->assertNotNull($current);
        $this->assertNull($current->end_date);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, '2025 – Actualidad'));
        $this->assertSame(1, substr_count($html, 'Puesto actual'));
        $this->assertStringNotContainsString('timeline__now', $html);
        $this->assertSame(0, preg_match_all('/\bActual\b/', $html));
    }

    public function test_roles_use_h3_and_section_has_single_h2(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="experiencia-title"'));
        $this->assertSame(4, substr_count($html, '<h3 class="timeline__role">'));

        foreach (Experience::query()->visible()->ordered()->get() as $experience) {
            $role = $experience->getTranslation('role', 'es');
            $this->assertNotSame('', trim($role));
            $this->assertSame(1, substr_count($html, '>'.e($role).'<'));
        }
    }

    public function test_home_uses_compact_variant_and_about_uses_full(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        $about = $this->get('/sobre-mi')->assertOk()->getContent();

        $this->assertStringContainsString('timeline--compact', $home);
        $this->assertStringContainsString('Ver trayectoria completa', $home);
        $this->assertStringNotContainsString('timeline--compact', $about);

        // Full about keeps the second paragraph of Musical Princesa.
        $this->assertStringContainsString('administración de servidores, repositorios privados', $about);
    }

    public function test_about_page_uses_same_roles(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        $about = $this->get('/sobre-mi')->assertOk()->getContent();

        foreach ([
            'Informático · Desarrollo de software y sistemas internos',
            'Cofundador y desarrollador web',
            'Desarrollador de software · Soporte técnico TIC',
            'Becario de desarrollo de software',
        ] as $role) {
            $this->assertStringContainsString($role, $home);
            $this->assertStringContainsString($role, $about);
        }
    }

    public function test_datetime_attributes_use_years(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('datetime="2025"', $html);
        $this->assertStringContainsString('datetime="2023"', $html);
        $this->assertStringContainsString('datetime="2019"', $html);
        $this->assertStringContainsString('datetime="2017"', $html);
    }

    public function test_company_links_are_valid(): void
    {
        $html = $this->get('/sobre-mi')->assertOk()->getContent();

        $this->assertStringContainsString('href="https://istcre.edu.ec/"', $html);
        $this->assertStringContainsString('href="https://algoritmun.com/"', $html);
        $this->assertStringContainsString('href="https://tienda.musicalprincesa.com"', $html);
    }
}
