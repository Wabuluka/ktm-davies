<?php

namespace App\Traits;

trait HandleConfig
{
    public function getConfigAsNullableString(string $key, mixed $default = null): ?string
    {
        return is_string($value = config($key, $default)) ? $value : null;
    }

    public function getConfigAsArray(string $key, mixed $default = null): array
    {
        return is_array($value = config($key, $default)) ? $value : [];
    }
}
