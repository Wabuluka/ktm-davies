<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    private $user;

    private $password = 'password'; // ファクトリーでpassword固定でユーザーが作られる

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    /**
     * ログイン画面にアクセスし、ログイン処理を行う
     *
     * @test
     */
    public function 正しいパスワードの場合(): void
    {
        // ログインページへアクセス
        $response = $this->get('/login');
        $response->assertOk();

        // ログインする
        $response = $this->post(route('login'), ['email' => $this->user->email, 'password' => $this->password]);
        // リダイレクトでページ遷移してくるのでstatusは302
        $response->assertStatus(Response::HTTP_FOUND);
        // リダイレクトで帰ってきた時のパス
        $response->assertRedirect('/books/');

        // このユーザーがログイン認証されているか
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * ユーザー一覧にアクセスし、ユーザー削除を行う
     *
     * @test
     */
    public function 正しくユーザー削除された(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/users');

        $response->assertOk();

        // 削除HTTPリクエスト
        $response = $this->delete(route('users.destroy', ['user' => $this->user]));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect('/users');

        // 削除処理
        $this->user->delete();
        // 論理削除されたか確認
        $this->assertSoftDeleted('users', [
            'id' => $this->user->id,
        ]);
    }
}
