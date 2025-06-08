<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetProductsHandler
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * @return Paginator<Product>
     */
    public function __invoke(GetProducts $message): Paginator
    {
        return $this->productRepository->getProductsPaginated($message->page);
    }
}
