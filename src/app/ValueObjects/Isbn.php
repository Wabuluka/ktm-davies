<?php

namespace App\ValueObjects;

use Stringable;

final class Isbn implements Stringable
{
    public function __construct(private string $isbn)
    {
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->isbn;
    }

    /**
     * ISBN 13 -> ISBN 10 に変換する
     */
    public function convert13To10(): self
    {
        if (mb_strlen($this->isbn) === 10) {
            return $this;
        }

        // 9文字抜き出し
        $prm_jan = substr($this->isbn, 3, 9);

        // チェックディジット計算
        $len = mb_strlen($prm_jan);
        $suuji_wa = 0;
        for ($i = 0; $i < $len; $i++) {
            $suuji_wa += (int) substr($prm_jan, $i, 1) * (10 - $i);
        }
        $check = 11 - $suuji_wa % 11;
        if ($check == 10) {
            // 余りが10の時はXをいれる
            $check = 'X';
        } elseif ($check > 10) {
            $check = '0';
        }

        $isbn10 = $prm_jan . $check;

        return new self($isbn10);
    }
}
