<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * プロフィールページを表示する
     */
    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    /**
     * プロフィールページで、ユーザー情報のユーザー名とメールアドレスを更新する
     *
     * name: ユーザー名
     * email: ユーザーメールアドレス
     * email_verified_at: メールアドレス認証年月日
     *
     * NOTE: ユーザー情報のemailを変更した時、変更前とデータが異なっていたら`email_verified_at`にnullが入る
     */
    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    /**
     * プロフィールページで、ユーザー情報のユーザー名と同値のメールアドレスを更新する
     *
     * name: ユーザー名
     * email: ユーザーメールアドレス
     *
     * NOTE: emailは同値で更新する為、`email_verified_at`はnullにならない
     */
    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /**
     * プロフィール画面で、ユーザー情報を削除する
     *
     * NOTE: プロフィール画面では、ユーザー情報を削除する際、パスワードを入力しないと削除できない仕様になっている
     */
    public function test_user_can_delete_their_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        // 論理削除されたか確認
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * プロフィール画面で、誤ったパスワードを入力してユーザー情報を削除する
     *
     * NOTE: エラーする事が前提のテストなので、`assertNotSoftDeleted`で論理削除されていない事、
     *       `assertSessionHasErrors`で正しくエラーになる事を確認する
     */
    public function test_correct_password_must_be_provided_to_delete_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        // 論理削除されていない事を確認
        $this->assertNotSoftDeleted($user);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
