<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndAuthorizationTest extends TestCase
{
    /**
     * Test public storefront is accessible by anyone.
     */
    public function test_public_storefront_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/shop');
        $response->assertStatus(200);
    }

    /**
     * Test guest can view login and register forms.
     */
    public function test_guest_can_view_login_and_register(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test guest cannot access admin routes.
     */
    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/products');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/categories');
        $response->assertRedirect('/login');
    }

    /**
     * Test user registration defaults to customer role.
     */
    public function test_user_registration_creates_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'Khách Hàng Mới',
            'email' => 'customer_test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'customer_test@example.com',
            'role' => 'customer',
        ]);
    }

    /**
     * Test admin login redirects to admin dashboard.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->post('/login', [
            'email' => 'admin@tuixach.vn',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test authenticated user cannot visit login or register pages.
     */
    public function test_authenticated_user_cannot_visit_auth_pages(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->get('/login');
        $response->assertRedirect();

        $response = $this->actingAs($admin)->get('/register');
        $response->assertRedirect();
    }

    /**
     * Test customer cannot access admin routes and gets 404 Not Found (security masking).
     */
    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(404);
    }

    /**
     * Test admin can access admin dashboard and CRUD routes.
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/products');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/categories');
        $response->assertStatus(200);
    }

    /**
     * Test logout logs the user out and redirects to home.
     */
    public function test_user_can_logout(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
