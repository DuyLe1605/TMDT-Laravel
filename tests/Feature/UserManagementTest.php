<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    /**
     * Test admin can view users index page.
     */
    public function test_admin_can_view_users_index(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Tài Khoản Người Dùng');
    }

    /**
     * Test admin can view user create form.
     */
    public function test_admin_can_view_user_create(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->get('/admin/users/create');
        $response->assertStatus(200);
        $response->assertSee('Thêm Tài Khoản Mới');
    }

    /**
     * Test admin can create a new user.
     */
    public function test_admin_can_create_new_user(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Nguyễn Văn Test',
            'email' => 'nguyenvan_test@tuixach.vn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nguyenvan_test@tuixach.vn',
            'name' => 'Nguyễn Văn Test',
            'role' => 'customer',
        ]);
    }

    /**
     * Test admin can update an existing user.
     */
    public function test_admin_can_update_user(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();
        $targetUser = User::where('email', 'nguyenvan_test@tuixach.vn')->first();

        if (!$targetUser) {
            $targetUser = User::factory()->create(['role' => 'customer']);
        }

        $response = $this->actingAs($admin)->put('/admin/users/' . $targetUser->id, [
            'name' => 'Nguyễn Văn Updated',
            'email' => $targetUser->email,
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nguyễn Văn Updated',
            'role' => 'admin',
        ]);
    }

    /**
     * Test admin cannot delete their own account.
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::where('email', 'admin@tuixach.vn')->first();

        $response = $this->actingAs($admin)->delete('/admin/users/' . $admin->id);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin@tuixach.vn',
        ]);
    }

    /**
     * Test non-admin gets 404 on user management routes.
     */
    public function test_non_admin_cannot_access_user_management(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $customer = User::factory()->create(['role' => 'customer']);
        }

        $response = $this->actingAs($customer)->get('/admin/users');
        $response->assertStatus(404);
    }
}
