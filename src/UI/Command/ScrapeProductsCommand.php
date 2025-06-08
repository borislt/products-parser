<?php

namespace App\UI\Command;

use App\Application\Command\ExportProductsToCsv;
use App\Application\Command\ScrapeProducts;
use App\Domain\Repository\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:scrape-products')]
class ScrapeProductsCommand extends Command
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
        private readonly string $productCategoryUrl,
        private readonly string $csvExportPath,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('start', InputArgument::OPTIONAL, 'First page', 1);
        $this->addArgument('end', InputArgument::OPTIONAL, 'Last page', 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->productRepository->cleanup();

        $firstPage = (int) $input->getArgument('start');
        $lastPage = (int) $input->getArgument('end');

        if ($firstPage > $lastPage) {
            $this->logger->error('First page must be less or equal to last page');

            return Command::FAILURE;
        }

        $totalPages = $lastPage - $firstPage + 1;

        $this->logger->info(
            sprintf('Start scraping %d pages from products category %s', $totalPages, $this->productCategoryUrl)
        );

        for ($page = $firstPage; $page <= $lastPage; $page++) {
            $url = sprintf('%s?page=%s', $this->productCategoryUrl, $page);
            $this->messageBus->dispatch(new ScrapeProducts($url, $page));
        }

        $this->logger->info('Finish scraping all pages');

        $this->logger->info(sprintf(
            'Dispatch ExportProductsToCsv task for background processing. Check %s directory for results',
            $this->csvExportPath,
        ));

        $this->messageBus->dispatch((new ExportProductsToCsv()));

        return Command::SUCCESS;
    }
}
