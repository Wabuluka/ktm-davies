<?php

namespace App\Interfaces;

use App\ValueObjects\Url;

interface CanOptimizeAnotherUrl
{
    public function optimizeAnotherUrl(Url $url): Url;
}
