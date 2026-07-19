<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::current();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Empresa Demo',
            'email' => 'demo@empresa.com',
            'need_type' => 'project',
            'message' => 'Necesito integrar Dolibarr con PrestaShop.',
            'consent' => '1',
        ], $overrides);
    }

    public function test_valid_submission_creates_lead_and_activity(): void
    {
        $response = $this->post('/contacto', $this->payload());

        $response->assertRedirect();
        $response->assertSessionHas('contact_success', true);
        $this->followRedirects($response)
            ->assertOk()
            ->assertSee(__('portfolio.contact.success_title'))
            ->assertSee(__('portfolio.contact.success'));
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('leads', ['email' => 'demo@empresa.com', 'status' => 'new']);
        $this->assertDatabaseCount('lead_activities', 1);
    }

    public function test_ajax_submission_returns_json_without_redirect(): void
    {
        $response = $this->postJson('/contacto', $this->payload());

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'title' => __('portfolio.contact.success_title'),
                'message' => __('portfolio.contact.success'),
            ]);
        $this->assertDatabaseCount('leads', 1);
    }

    public function test_validation_errors_are_flashed_for_the_user(): void
    {
        $response = $this->from('/contacto')->post('/contacto', $this->payload([
            'name' => '',
            'email' => 'no-es-email',
            'message' => '',
            'consent' => null,
        ]));

        $response->assertRedirect('/contacto');
        $response->assertSessionHasErrors(['name', 'email', 'message', 'consent']);
    }

    public function test_missing_consent_fails_validation(): void
    {
        $response = $this->post('/contacto', $this->payload(['consent' => null]));

        $response->assertSessionHasErrors('consent');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_honeypot_blocks_spam_without_creating_lead(): void
    {
        $response = $this->post('/contacto', $this->payload(['website' => 'http://spam.example']));

        $response->assertSessionHasErrors('website');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_rate_limiting_blocks_excessive_submissions(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/contacto', $this->payload(['email' => "user{$i}@example.com"]));
        }

        $this->post('/contacto', $this->payload(['email' => 'blocked@example.com']))
            ->assertStatus(429);
    }
}
