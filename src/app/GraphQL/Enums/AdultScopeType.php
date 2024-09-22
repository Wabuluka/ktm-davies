<?php

namespace App\GraphQL\Enums;

enum AdultScopeType
{
    case INCLUDE;
    case EXCLUDE;
    case ONLY;
}
