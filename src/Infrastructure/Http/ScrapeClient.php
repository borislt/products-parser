<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\ScrapeClientException;
use App\Application\ScrapeClientInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ScrapeClient implements ScrapeClientInterface
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function fetchPage(string $url): Crawler
    {
        try {
            $response = $this->client->request(Request::METHOD_GET, $url);
            $html = $response->getContent();
        } catch (ExceptionInterface $exception) {
            throw new ScrapeClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return new Crawler($html);
    }
}
