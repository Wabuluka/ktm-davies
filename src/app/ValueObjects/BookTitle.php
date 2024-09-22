<?php

namespace App\ValueObjects;

use Exception;
use Stringable;

final class BookTitle implements Stringable
{
    /** @var string[] 表記ゆれが起きやすい文字 */
    public const ORTHOGRAPHICAL_VARIANTS = ['～', '、', '―', '－', 'THE COMIC', '!!', '　'];

    public function __construct(private string $name, private ?string $volume = null)
    {
    }

    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * 書誌名を返却する
     */
    public function value(): string
    {
        return trim($this->name);
    }

    /**
     * 文字コードを EUC-JP に変換する
     */
    public function convertToEucJp(): self
    {
        $converted = mb_convert_encoding($this->value(), 'EUC-JP', 'UTF-8');

        return $this->newInstance($converted);
    }

    /**
     * 書誌名のアポストロフィを統一する
     * (' と ’ の違いで検索結果に出てこない場合があるため)
     */
    public function orthographizeApostrophe(string $apostrophe): self
    {
        if (! in_array($apostrophe, ["'", '’'])) {
            throw new Exception("引数には ' と ’ のみ指定可能です");
        }

        $orthographized = str_replace(["'", '’'], $apostrophe, $this->value());

        return $this->newInstance($orthographized);
    }

    /**
     * 書誌名末尾の巻数の書式を統一する
     * (表記の違いで検索結果に出てこない場合があるため)
     */
    public function orthographizeVolume(): self
    {
        $removed = $this->volumeDetachedName();

        return $this->volume
            ? $this->newInstance($removed . ' ' . mb_convert_kana($this->volume, 'n', 'UTF-8'))
            : $this->newInstance($removed);
    }

    /**
     * 文字列を空白に置換する
     *
     * @param  string[]  $search
     */
    public function replaceToWhiteSpace(array $search): self
    {
        $replaced = str_replace($search, ' ', $this->name);

        return $this->newInstance($replaced);
    }

    /**
     * 書誌名から巻数を削除する
     * (単話やシリーズでの販売サイトで検索結果に出てこない場合があるため)
     */
    public function removeVolume(): self
    {
        $removed = $this->volumeDetachedName();

        return $this->newInstance($removed);
    }

    /**
     * 書誌名を $length 文字に切り詰める
     */
    public function truncate(int $length): self
    {
        $trucated = mb_substr($this->value(), 0, $length);

        return $this->newInstance($trucated);
    }

    private function newInstance(string $new_value): self
    {
        return new self($new_value, $this->volume);
    }

    private function volumeDetachedName(): string
    {
        if ($this->volume === null) {
            return $this->value();
        }

        $mb_strrev = function (string $str) {
            $r = '';
            for ($i = mb_strlen($str); $i >= 0; $i--) {
                $r .= mb_substr($str, $i, 1);
            }

            return $r;
        };

        // 巻数(volume)が全角・半角どちらでも削除する
        $volume_han = mb_convert_kana($this->volume, 'n', 'UTF-8');
        $volume_zen = mb_convert_kana($this->volume, 'N', 'UTF-8');
        $volume_rev = $mb_strrev($volume_han);
        $volume_zen_rev = $mb_strrev($volume_zen);

        // 巻数文字列とタイトルの間にスペースがない場合
        $reg_no_space = '^[' . $volume_rev . '|' . $volume_zen_rev . ']';
        // 巻数文字列とタイトルの間にスペースがある場合 (Vol.n, 第n話, n巻, (*n*), 【*n*】, [*n*])
        $reg_has_space = '^(.*?(' . $volume_rev . '|' . $volume_zen_rev . ')\s*((）|\)|\]|」|】)*?(（|\(|\[|「)|\s?\.?lov(（|\(|\[|「|【)?|第|【|\s| ))';

        // HACK: 正規表現で末尾の文字列を指定する方法が分からなかったため、文字列を反転している
        $bookname_rev = $mb_strrev($this->name);
        $bookname_volume_removed_rev = mb_eregi_replace($reg_has_space . '|' . $reg_no_space, '', $bookname_rev) ?: '';
        $bookname_volume_removed = $mb_strrev($bookname_volume_removed_rev);

        // 巻末の 「上」 「下」 削除
        $bookname_volume_removed = mb_eregi_replace('\s+(上|下).*$', '', $bookname_volume_removed) ?: '';

        return rtrim($bookname_volume_removed);
    }
}
