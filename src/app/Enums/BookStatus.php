<?php

namespace App\Enums;

enum BookStatus: string
{
    case Draft = 'draft';
    case WillBePublished = 'willBePublished';
    case Published = 'published';
}
