<?php

namespace App\Enums;

use App\Models\LabelType as ModelsLabelType;

enum LabelType: int
{
    case Paperback = 1;
    case Magazine = 2;
    case Goods = 3;
    case Unknown = 0;

    public static function fromModel(ModelsLabelType $labelType): static
    {
        return self::tryFrom($labelType->id) ?: self::Unknown;
    }
}
