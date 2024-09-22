<?php

namespace App\Interfaces;

use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

interface CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url;
}
