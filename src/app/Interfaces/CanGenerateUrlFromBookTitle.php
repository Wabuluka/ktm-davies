<?php

namespace App\Interfaces;

use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

interface CanGenerateUrlFromBookTitle
{
    public function generateUrlFromBookTitle(BookTitle $title): Url;
}
