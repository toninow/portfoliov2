<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => bcrypt('secret-password'),
            'role' => 'super_admin',
        ]);
    }

    public function test_login_page_is_reachable(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_area_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_open_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }

    public function test_admin_can_open_project_and_lead_resources(): void
    {
        $this->seed(PortfolioSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/projects')->assertOk();
        $this->actingAs($admin)->get('/admin/leads')->assertOk();
        $this->actingAs($admin)->get('/admin/services')->assertOk();
    }

    public function test_non_admin_cannot_access_panel(): void
    {
        $user = User::create([
            'name' => 'Nobody',
            'email' => 'nobody@test.local',
            'password' => bcrypt('secret-password'),
            'role' => 'none',
        ]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
