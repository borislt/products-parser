<?php

declare(strict_types=1);

namespace App\Application;

use Symfony\Component\DomCrawler\Crawler;

interface ScrapeClientInterface
{
    public function fetchPage(string $url): Crawler;
}
