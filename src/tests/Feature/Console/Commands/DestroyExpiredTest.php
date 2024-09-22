<?php

namespace Tests\Feature\Console\Commands;

use App\Models\BookPreview;
use App\Models\NewsPreview;
use App\Models\PagePreview;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DestroyExpiredTest extends TestCase
{
    /** @test */
    public function 有効期限が切れた書籍のプレビューのみ削除すること(): void
    {
        // NOTE: 2 時間以内に作成されたプレビューのみ保持する
        Config::set('preview.expire_hours', 2);

        $preview1 = BookPreview::factory()->create();

        $this->travel(1)->hours();

        $preview2 = BookPreview::factory()->create();

        $this->travel(1)->hours();

        $preview3 = BookPreview::factory()->create();

        $this->artisan('app:preview:destroy-expired Book')->assertExitCode(0);

        $this->assertModelMissing($preview1);
        $this->assertModelExists($preview2);
        $this->assertModelExists($preview3);
    }

    /** @test */
    public function 有効期限が切れたNewsのプレビューのみ削除すること(): void
    {
        Config::set('preview.expire_hours', 2);

        $preview1 = NewsPreview::factory()->create();

        $this->travel(1)->hours();

        $preview2 = NewsPreview::factory()->create();

        $this->travel(1)->hours();

        $preview3 = NewsPreview::factory()->create();

        $this->artisan('app:preview:destroy-expired News')->assertExitCode(0);

        $this->assertModelMissing($preview1);
        $this->assertModelExists($preview2);
        $this->assertModelExists($preview3);
    }

    /** @test */
    public function 有効期限が切れた固定ページのプレビューのみ削除すること(): void
    {
        Config::set('preview.expire_hours', 2);

        $preview1 = PagePreview::factory()->create();

        $this->travel(1)->hours();

        $preview2 = PagePreview::factory()->create();

        $this->travel(1)->hours();

        $preview3 = PagePreview::factory()->create();

        $this->artisan('app:preview:destroy-expired Page')->assertExitCode(0);

        $this->assertModelMissing($preview1);
        $this->assertModelExists($preview2);
        $this->assertModelExists($preview3);
    }

    /** @test */
    public function 一度のコマンド実行で複数種類のプレビューを削除できること(): void
    {
        Config::set('preview.expire_hours', 2);

        $bookPreview1 = BookPreview::factory()->create();
        $newsPreview1 = NewsPreview::factory()->create();
        $pagePreview1 = PagePreview::factory()->create();

        $this->travel(1)->hours();

        $bookPreview2 = BookPreview::factory()->create();
        $newsPreview2 = NewsPreview::factory()->create();
        $pagePreview2 = PagePreview::factory()->create();

        $this->travel(1)->hours();

        $bookPreview3 = BookPreview::factory()->create();
        $newsPreview3 = NewsPreview::factory()->create();
        $pagePreview3 = PagePreview::factory()->create();

        $this->artisan('app:preview:destroy-expired Book News Page')->assertExitCode(0);

        $this->assertModelMissing($bookPreview1);
        $this->assertModelMissing($newsPreview1);
        $this->assertModelMissing($pagePreview1);
        $this->assertModelExists($bookPreview2);
        $this->assertModelExists($newsPreview2);
        $this->assertModelExists($pagePreview2);
        $this->assertModelExists($bookPreview3);
        $this->assertModelExists($newsPreview3);
        $this->assertModelExists($pagePreview3);
    }

    /** @test */
    public function 書籍プレビューの削除と同時に、プレビュー用の画像も削除されること(): void
    {
        Config::set('preview.expire_hours', 2);

        BookPreview::factory()->create([
            'file_paths' => [
                $path = UploadedFile::fake()->image('')->storeAs('tmp/preview', 'cover.jpg', 'public'),
            ],
        ]);

        $this->travel(2)->hours();

        $this->artisan('app:preview:destroy-expired Book')->assertExitCode(0);

        Storage::disk('public')->assertMissing($path);
    }

    /** @test */
    public function Newsプレビューの削除と同時に、プレビュー用の画像も削除されること(): void
    {
        Config::set('preview.expire_hours', 2);

        NewsPreview::factory()->create([
            'file_paths' => [
                $path = UploadedFile::fake()->image('')->storeAs('tmp/preview', 'eyecatch.jpg', 'public'),
            ],
        ]);

        $this->travel(2)->hours();

        $this->artisan('app:preview:destroy-expired News')->assertExitCode(0);

        Storage::disk('public')->assertMissing($path);
    }
}
