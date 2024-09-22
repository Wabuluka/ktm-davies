<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class NewsDestroyTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $news = News::factory()->create();
        $response = $this->delete(route('news.destroy', $news));
        $response->assertRedirect('/login');
        $this->assertModelExists($news);
    }

    /** @test */
    public function ログイン状態であればNEWSを削除できること(): void
    {
        $news = News::factory()->create();
        $response = $this->login()->delete(route('news.destroy', $news));
        $response->assertRedirectToRoute('sites.news.index', $news->category->site);
        $this->assertModelMissing($news);
    }

    /** @test */
    public function 紐付けているアイキャッチ画像も削除されること(): void
    {
        $news = News::factory()->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg')
        )->create();
        $response = $this->login()->delete(route('news.destroy', $news));
        $response->assertRedirectToRoute('sites.news.index', $news->category->site);
        $this
            ->assertModelMissing($news)
            ->assertDatabaseMissing(Media::class, [
                'model_type' => 'news',
                'model_id' => $news->id,
            ]);
    }
}
