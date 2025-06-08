<?php

declare(strict_types=1);

namespace App\Application\Command;

class ScrapeProducts
{
    public function __construct(public string $url, public int $page)
    {
    }
}
