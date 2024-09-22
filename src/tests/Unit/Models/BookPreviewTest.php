<?php

namespace Tests\Unit\Models;

use App\Models\BookPreview;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookPreviewTest extends TestCase
{
    /** @test */
    public function モデルの削除時にプレビュー用の画像が削除されること()
    {
        $path = UploadedFile::fake()->image('')->storeAs('tmp/preview', 'cover.jpg', 'public');
        $preview = BookPreview::factory()->create(['file_paths' => [$path]]);

        Storage::disk('public')->assertExists($path);
        $preview->delete();
        Storage::disk('public')->assertMissing($path);
    }
}
