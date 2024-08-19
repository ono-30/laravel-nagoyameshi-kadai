<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    use RefreshDatabase;

    /* 未ログインのユーザーは管理者用「会員一覧」ページにアクセスできない*/
    public function test_guest_cannot_access_admin_users_index()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('admin.login'));
    }

    /* ログイン済みの一般ユーザーも管理者用「会員一覧」ページにアクセスできない*/
    public function test_user_cannot_access_admin_users_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertRedirect(route('admin.login'));
    }

    /* ログイン済みの管理者は管理者用「会員一覧」ページにアクセスできる*/
    public function test_admin_can_access_admin_users_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /* 未ログインのユーザーは管理者用「会員詳細」ページにアクセスできない*/
    public function test_guest_cannot_access_admin_users_show()
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.show', $user));

        $response->assertRedirect(route('admin.login'));
    }

    /* ログイン済みの一般ユーザーも管理者用「会員詳細」ページにアクセスできない*/
    public function test_user_cannot_access_admin_users_show()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.show', $user));

        $response->assertRedirect(route('admin.login'));
    }

    /* ログイン済みの管理者は管理者用「会員詳細」ページにアクセスできる*/
    public function test_admin_can_access_admin_users_show()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));

        $response->assertStatus(200);
    }
}
