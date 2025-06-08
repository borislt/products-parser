<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\ScrapeClientException;
use App\Application\ScrapeClientInterface;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ScrapeProductsHandler
{
    public function __construct(
        private ScrapeClientInterface $scraper,
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ScrapeProducts $message): void
    {
        $this->logger->info(sprintf('Start scraping page %d', $message->page));

        try {
            $domCrawler = $this->scraper->fetchPage($message->url);
        } catch (ScrapeClientException $exception) {
            $this->logger->error('Failed to get page contents. Skipping..', [
                'url' => $message->url,
                'exception' => $exception->getMessage(),
            ]);

            return;
        }

        $baseUrl = $this->getBaseUrl($message->url);
        $products = [];

        $this->logger->info(sprintf('Start parsing html contents of page %d', $message->page));

        $domCrawler->filterXPath('//div[contains(@class, "ProductsBox__listItem")]')->each(
            function (Crawler $node) use ($baseUrl, &$products) {
                $name = $node->filterXPath('.//span[contains(@class, "ProductTile__title")]')->text('No name');
                $price = $node->filterXPath('.//span[contains(@class, "Price__value_caption")]')->text('No price');
                $path = $node->filterXPath('.//a[contains(@class, "ProductTileLink")]/@href')->text('No path');
                $image = $node->filterXPath('.//div[contains(@class, "ProductTile__imageContainer")]//img/@src')->text('No image');

                $products[] = new Product(
                    $name,
                    $price,
                    sprintf('%s%s', $baseUrl, $path),
                    $image,
                );
            }
        );

        $this->productRepository->storeAll($products);
        $this->logger->info(sprintf('Saved %d products to database', count($products)));

        $this->logger->info(sprintf('Finish scraping page %d', $message->page));
    }

    private function getBaseUrl(string $url): string
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
            throw new InvalidArgumentException(sprintf('Failed to parse url %s', $url));
        }

       return $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    }
}
