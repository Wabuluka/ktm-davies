<?php

namespace Tests\Feature\Http\Controllers\Folder;

use App\Models\Folder;
use Tests\TestCase;

class FolderStoreTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->post('/api/folders', [
            'name' => 'Folder',
        ]);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍作成ページを表示すること(): void
    {
        $response = $this->login()->post('/api/folders', [
            'name' => 'Folder',
            'parent_id' => 'folder_1',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function 同一階層に同じ名前のフォルダを作成できないこと(): void
    {
        $this->login();
        $this->assertDatabaseCount('folders', 1);

        $response1 = $this->post('/api/folders', [
            'name' => 'foo',
            'parent_id' => 'folder_1',
        ]);
        $response2 = $this->post('/api/folders', [
            'name' => 'foo',
            'parent_id' => 'folder_1',
        ]);
        $foo = Folder::first()->children()->first();
        $response3 = $this->post('/api/folders', [
            'name' => 'foo',
            'parent_id' => "folder_{$foo->id}",
        ]);

        $response1->assertOk();
        $response2->assertSessionHasErrors('name');
        $response3->assertOk();
        $this->assertDatabaseCount('folders', 1 + 2);
    }

    /** @test */
    public function 親フォルダが未指定の場合フォルダを作成できないこと(): void
    {
        $this->login();
        $this->assertDatabaseCount('folders', 1);

        $response = $this->post('/api/folders', [
            'name' => 'foo',
        ]);

        $response->assertSessionHasErrors('parent_id');
        $this->assertDatabaseCount('folders', 1);
    }
}
