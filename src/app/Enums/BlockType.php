<?php

namespace App\Enums;

enum BlockType: int
{
    case Common = 1;
    case BookStore = 2;
    case EbookStore = 3;
    case Benefit = 4;
    case Series = 5;
    case Related = 6;
    case Story = 7;
    case Character = 8;
    case Custom = 9;
}
