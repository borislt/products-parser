<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Generator;

interface ProductRepositoryInterface
{
    const int PAGE_SIZE = 30;

    public function cleanup(): void;

    /**
     * @param array<Product> $products
     */
    public function storeAll(array $products): void;

    public function getGenerator(): Generator;

    /**
     * @return Paginator<Product>
     */
    public function getProductsPaginated(int $page): Paginator;
}
