<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\CsvExportException;
use App\Domain\Repository\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ExportProductsToCsvHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger,
        private string $csvExportPath,
    ) {}

    public function __invoke(ExportProductsToCsv $message): void
    {
        $filepath = sprintf('%s/products.csv', $this->csvExportPath);

        $this->logger->info(sprintf('Start export products from database to %s', $filepath));

        if (!is_dir($this->csvExportPath)) {
            $isDirectory = mkdir($this->csvExportPath, 0775, true);

            if (!$isDirectory) {
                throw new CsvExportException(sprintf('Failed to create directory %s', $this->csvExportPath));
            }
        }

        $file = fopen($filepath, 'w');

        if (!$file) {
            throw new CsvExportException(sprintf('Failed to open %s file for writing', $filepath));
        }

        fputcsv($file, ['id', 'name', 'price', 'url', 'image']);
        foreach ($this->productRepository->getGenerator() as $product) {
            fputcsv($file, [
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
                $product->getUrl(),
                $product->getImage(),
            ]);
        }

        fclose($file);

        $this->logger->info(sprintf('Finish export products from database to %s', $filepath));
    }
}
