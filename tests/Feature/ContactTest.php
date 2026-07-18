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
            'message' => 'Necesito integrar Dolibarr con PrestaShop.',
            'consent' => '1',
        ], $overrides);
    }

    public function test_valid_submission_creates_lead_and_activity(): void
    {
        $response = $this->post('/contacto', $this->payload());

        $response->assertRedirect();
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('leads', ['email' => 'demo@empresa.com', 'status' => 'new']);
        $this->assertDatabaseCount('lead_activities', 1);
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
