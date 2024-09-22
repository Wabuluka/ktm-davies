<?php

namespace App\ValueObjects;

use Stringable;

final class Url implements Stringable
{
    private ?string $scheme;

    private ?string $host;

    private ?int $port;

    private ?string $user;

    private ?string $pass;

    private ?string $path;

    private array $searchParams;

    private ?string $fragment;

    private int $encodeTimes = 0;

    public function __construct(private string $url)
    {
        $result = parse_url($url) ?: [];
        if (! is_array($result)) {
            $this->scheme = null;
            $this->host = null;
            $this->port = null;
            $this->user = null;
            $this->pass = null;
            $this->path = null;
            $this->searchParams = [];
            $this->fragment = null;

            return;
        }
        $asNullableString = fn (string $key): ?string => is_string($value = $result[$key] ?? null) ? $value : null;
        $asNullableInteger = fn (string $key): ?int => is_int($value = $result[$key] ?? null) ? $value : null;
        parse_str($asNullableString('query') ?? '', $searchParams);
        $this->scheme = $asNullableString('scheme');
        $this->host = $asNullableString('host');
        $this->port = $asNullableInteger('port');
        $this->user = $asNullableString('user');
        $this->pass = $asNullableString('pass');
        $this->path = $asNullableString('path');
        $this->searchParams = $searchParams;
        $this->fragment = $asNullableString('fragment');
    }

    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * URL にクエリパラメータを追加する (既存のパラメータとキーが重複しているパラメータは追加されない)
     */
    public function addSearchParams(array $searchParams): self
    {
        foreach ($searchParams as $key => $value) {
            if (isset($this->searchParams[$key])) {
                continue;
            }
            $this->searchParams[$key] = $value;
        }

        return $this;
    }

    /**
     * URL のパスを上書きする
     *
     * @param  string|string[]  $path
     */
    public function setPath(string|array $path, array|string $separator = '/'): self
    {
        $this->path = is_string($path) ? $path : '/' . implode($separator, $path);

        return $this;
    }

    /**
     * URL をパーセントエンコードする
     */
    public function encode(int $times = 1): self
    {
        $this->encodeTimes += $times;

        return $this;
    }

    public function value(): string
    {
        $url = (
            $this->scheme
                ? "{$this->scheme}://"
                : ''
        ) . (
            ($this->user && $this->pass)
                ? "{$this->user}:{$this->pass}@"
                : ''
        ) . (
            $this->host
                ? "{$this->host}"
                : ''
        ) . (
            $this->port
                ? ":{$this->port}"
                : ''
        ) . (
            $this->path
                ? "{$this->path}"
                : ''
        ) . (
            count($this->searchParams) > 0
                ? '?' . http_build_query($this->searchParams)
                : ''
        ) . (
            $this->fragment
                ? "#{$this->fragment}"
                : ''
        );

        for ($i = 0; $i < $this->encodeTimes; $i++) {
            $url = rawurlencode($url);
        }

        return $url;
    }
}
