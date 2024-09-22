<?php

namespace App\Enums;

enum NewsStatus: string
{
    case Draft = 'draft';
    case WillBePublished = 'willBePublished';
    case Published = 'published';
}
