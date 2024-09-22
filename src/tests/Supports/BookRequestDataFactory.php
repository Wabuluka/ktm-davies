<?php

namespace Tests\Supports;

use App\Enums\BlockType;
use App\Enums\BookStatus;
use App\Models\Benefit;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookStore;
use App\Models\Character;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\EbookStore;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use App\Models\Story;

/**
 * Book の作成・更新リクエストに用データを生成する
 */
final class BookRequestDataFactory
{
    private array $data;

    private array $keys;

    private array $outputDataKeys;

    private static int $newRelatedItemId = 1;

    private static int $newBlockId = 1;

    private function __construct(array $customData = [])
    {
        $default = [
            // 書籍名設定
            'title' => 'The Great Gatsby',
            'title_kana' => 'ザ・グレート・ギャツビー',
            'volume' => null,

            // 公開設定
            'sites' => [],
            'status' => 'published',
            'published_at' => now(),

            // 作品情報
            'cover' => null,
            'label_id' => null,
            'genre_id' => null,
            'series_id' => null,

            'creations' => [],

            'isbn13' => null,
            'release_date' => null,
            'price' => null,
            'format_id' => null,
            'size_id' => null,
            'ebook_only' => false,
            'special_edition' => false,
            'limited_edition' => false,
            'adult' => false,

            // 詳細情報
            'caption' => null,
            'description' => null,
            'keywords' => null,
            'trial_url' => null,
            'survey_url' => null,

            // 表示設定

            //// 購入先情報（紙） ////
            'bookstores' => [],

            //// 購入先情報（電子） ////
            'ebookstores' => [],

            //// 店舗特典 ////
            'benefits' => [],

            //// 収録作品 ////
            'stories' => [],

            //// キャラクター ////
            'characters' => [],

            //// 関連作品 ////
            'related_items' => [
                'upsert' => [],
                'deleteIds' => [],
            ],

            //// ブロック ////
            'blocks' => [
                'upsert' => [],
                'deleteIds' => [],
            ],
        ];
        $this->keys = array_keys($default);
        $this->outputDataKeys = $this->keys;
        $this->setStatus(BookStatus::Published);
        $this->except(
            'cover', 'sites', 'creations', 'bookstores', 'ebookstores', 'benefits', 'stories', 'characters',
        );
        $this->ensureValidKey(...array_keys($customData));
        $this->data = [...$default, ...$customData];
    }

    /**
     * データのキーが有効か検証する
     */
    private function ensureValidKey(string ...$keys): void
    {
        foreach ($keys as $key) {
            if (! in_array($key, $this->keys, true)) {
                throw new \Exception("Invalid data key given: {$key}");
            }
        }
    }

    /**
     * 新規ファクトリを生成する
     */
    public static function new(array $customData = []): self
    {
        $instance = new self($customData);

        return $instance;
    }

    /**
     * リクエストデータを生成する
     */
    public function create(array $customData = []): array
    {
        $this->ensureValidKey(...array_keys($customData));
        $hasValidKey = fn ($key) => in_array($key, $this->outputDataKeys, true);
        $data = array_filter($this->data, $hasValidKey, ARRAY_FILTER_USE_KEY);

        return [...$data, ...$customData];
    }

    /**
     * リクエストデータに特定のデータのみ含める
     */
    public function only(string ...$keys): self
    {
        $this->ensureValidKey(...$keys);
        $this->outputDataKeys = $keys;

        return $this;
    }

    /**
     * リクエストデータから特定のデータを除外する
     */
    public function except(string ...$keys): self
    {
        $this->ensureValidKey(...$keys);
        $this->outputDataKeys = array_diff($this->outputDataKeys, $keys);

        return $this;
    }

    /**
     * リクエストデータから特定のデータを復元する
     */
    private function restore(string ...$keys): self
    {
        $this->ensureValidKey(...$keys);
        $this->outputDataKeys = array_unique([...$this->outputDataKeys, ...$keys]);

        return $this;
    }

    /**
     * リクエストデータにステータス情報を追加する
     */
    public function setStatus(BookStatus $status, \DateTimeInterface $publishedAt = null): self
    {
        $publishedAt ??= now()->addMonth();
        now()->lte($publishedAt) ?: throw new \Exception('公開予定日は現在時刻よりも先にしてください。');
        $this->data['status'] = $status->value;
        $this->restore('status');

        $newData = $this;
        if ($status === BookStatus::Draft) {
            $newData->except('published_at');
        }
        if ($status === BookStatus::WillBePublished) {
            $this->data['published_at'] = $publishedAt;
            $this->restore('published_at');
        }
        if ($status === BookStatus::Published) {
            $newData->except('published_at');
        }

        return $this;
    }

    /**
     * リクエストデータに作家情報を追加する
     */
    public function addCreation(
        array|Creator $creator,
        string|CreationType $type,
        string $displayedType = null,
        int $sort = null
    ): self {
        $creatorId = $creator instanceof Creator
            ? $creator->id
            : Creator::factory()->create($creator)->id;
        $typeName = $type instanceof CreationType
            ? $type->name
            : CreationType::firstOrCreate(['name' => $type])->name;
        $sort = $sort
            ?? count($this->data['creations']) + 1;

        $this->data['creations'][] = [
            'creator_id' => $creatorId,
            'creation_type' => $typeName,
            'displayed_type' => $displayedType,
            'sort' => $sort,
        ];

        $this->restore('creations');

        return $this;
    }

    /**
     * リクエストデータに購入先情報（紙）を追加する
     */
    public function addBookStore(
        array|BookStore $store,
        string $url = null,
        bool $isPrimary = false,
    ): self {
        $storeId = $store instanceof BookStore
            ? $store->id
            : BookStore::factory()->create($store)->id;

        $this->data['bookstores'][] = [
            'id' => $storeId,
            'url' => $url,
            'is_primary' => $isPrimary,
        ];

        $this->restore('bookstores');

        return $this;
    }

    /**
     * リクエストデータに購入先情報（電子）を追加する
     */
    public function addEbookStore(
        array|EbookStore $store,
        string $url = null,
        bool $isPrimary = false,
    ): self {
        $storeId = $store instanceof EbookStore
            ? $store->id
            : EbookStore::factory()->create($store)->id;

        $this->data['ebookstores'][] = [
            'id' => $storeId,
            'url' => $url,
            'is_primary' => $isPrimary,
        ];

        $this->restore('ebookstores');

        return $this;
    }

    /**
     * リクエストデータに店舗特典情報を追加する
     */
    public function addBenefit(array|Benefit $benefit): self
    {
        $id = $benefit instanceof Benefit
            ? $benefit->id
            : Benefit::factory()->create($benefit)->id;
        $this->data['benefits'][] = ['id' => $id];
        $this->restore('benefits');

        return $this;
    }

    /**
     * リクエストデータに収録作品情報を追加する
     */
    public function addStory(array|Story $story): self
    {
        $id = $story instanceof Story
            ? $story->id
            : Story::factory()->create($story)->id;
        $this->data['stories'][] = ['id' => $id];
        $this->restore('stories');

        return $this;
    }

    /**
     * リクエストデータにキャラクター情報を追加する
     */
    public function addCharacter(array|Character $character): self
    {
        $id = $character instanceof Character
            ? $character->id
            : Character::factory()->create($character)->id;
        $this->data['characters'][] = ['id' => $id];
        $this->restore('characters');

        return $this;
    }

    /**
     * リクエストデータに新規作成する関連作品の情報を追加する
     */
    public function addNewRelatedItem(
        Book|ExternalLink $relatable,
        string $description,
        int $sort = null
    ): self {
        $id = '+' . self::$newRelatedItemId++;
        [$relatable_id, $relatable_type] = match (get_class($relatable)) {
            Book::class => [$relatable->id, 'book'],
            ExternalLink::class => [$relatable->id, 'externalLink'],
        };
        $sort = $sort
            ?? count($this->data['related_items']['upsert']) + 1;

        $this->data['related_items']['upsert'][] = compact(
            'id',
            'relatable_id',
            'relatable_type',
            'description',
            'sort',
        );

        return $this;
    }

    /**
     * リクエストデータに更新する関連作品の情報を追加する
     */
    public function addUpdatedRelatedItem(
        RelatedItem $relatedItem,
        Book|ExternalLink $relatable = null,
        string $description = null,
        int $sort = null
    ): self {
        if ($relatable) {
            [$relatable_id, $relatable_type] = match (get_class($relatable)) {
                Book::class => [$relatable->id, 'book'],
                ExternalLink::class => [$relatable->id, 'externalLink'],
            };
            $relatedItem->relatable_id = $relatable_id;
            $relatedItem->relatable_type = $relatable_type;
        }
        if ($description) {
            $relatedItem->description = $description;
        }
        if ($sort) {
            $relatedItem->sort = $sort;
        }

        $this->data['related_items']['upsert'][] = $relatedItem->only([
            'id',
            'relatable_id',
            'relatable_type',
            'description',
            'sort',
        ]);

        return $this;
    }

    /**
     * リクエストデータに削除する関連作品の情報を追加する
     */
    public function addDeletedRelatedItem(
        RelatedItem $relatedItem,
    ): self {
        $this->data['related_items']['deleteIds'][] = $relatedItem->id;

        return $this;
    }

    /**
     * リクエストデータにブロックの情報を追加する
     */
    private function addBlock(
        BlockType $blockType,
        string $custom_title = null,
        string $custom_content = null,
        int $sort = null,
        bool $displayed = true,
    ): self {
        $id = '+' . self::$newBlockId++;
        $type_id = $blockType->value;
        $sort = $sort
            ?? count($this->data['blocks']['upsert']) + 1;

        $this->data['blocks']['upsert'][] = compact(
            'id',
            'type_id',
            'custom_title',
            'custom_content',
            'sort',
            'displayed'
        );

        return $this;
    }

    /**
     * リクエストデータに商品本体ブロックの情報を追加する
     */
    public function addCommonBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Common, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータに購入先ブロックの情報を追加する
     */
    public function addBookStoreBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::BookStore, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータに購入先 (電子) ブロックの情報を追加する
     */
    public function addEbookStoreBlock(
        string $customContent = null,
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::EbookStore, null, $customContent, $sort, $displayed);
    }

    /**
     * リクエストデータに店舗特典ブロックの情報を追加する
     */
    public function addBenefitBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Benefit, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータにシリーズ一覧ブロックの情報を追加する
     */
    public function addSeriesBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Series, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータに関連作品ブロックの情報を追加する
     */
    public function addRelatedBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Related, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータに収録作品ブロックの情報を追加する
     */
    public function addStoryBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Story, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータにキャラクター紹介ブロックの情報を追加する
     */
    public function addCharacterBlock(
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Character, null, null, $sort, $displayed);
    }

    /**
     * リクエストデータに自由欄ブロックの情報を追加する
     */
    public function addCustomBlock(
        string $customTitle,
        string $customContent,
        int $sort = null,
        bool $displayed = true,
    ): self {
        return $this->addBlock(BlockType::Custom, $customTitle, $customContent, $sort, $displayed);
    }

    /**
     * リクエストデータに更新するブロックの情報を追加する
     */
    public function addUpdatedBlock(
        Block $block,
        string $customTitle = null,
        string $customContent = null,
        int $sort = null,
        bool $displayed = null,
    ): self {
        if ($customTitle !== null) {
            $block->custom_title = $customTitle;
        }
        if ($customContent !== null) {
            $block->custom_content = $customContent;
        }
        if ($sort !== null) {
            $block->sort = $sort;
        }
        if ($displayed !== null) {
            $block->displayed = $displayed;
        }

        $this->data['blocks']['upsert'][] = [
            ...$block->only([
                'id',
                'custom_title',
                'custom_content',
                'sort',
                'displayed',
            ]),
            'type_id' => $block->type->value,
        ];

        return $this;
    }

    /**
     * リクエストデータに削除するブロックの情報を追加する
     */
    public function addDeletedBlock(
        Block $block,
    ): self {
        $this->data['blocks']['deleteIds'][] = $block->id;

        return $this;
    }
}
