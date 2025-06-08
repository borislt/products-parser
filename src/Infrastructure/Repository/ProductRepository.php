<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Generator;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function cleanup(): void
    {
        $this->createQueryBuilder('product')->delete()->getQuery()->execute();
    }

    public function storeAll(array $products): void
    {
        foreach ($products as $product) {
            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    public function getGenerator(): Generator
    {
        $query = $this->createQueryBuilder('product')->getQuery();

        foreach ($query->toIterable() as $product) {
            yield $product;
            $this->getEntityManager()->detach($product);
        }
    }

    public function getProductsPaginated(int $page): Paginator
    {
        $query = $this->createQueryBuilder('product')->getQuery();
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult(self::PAGE_SIZE * ($page - 1))
            ->setMaxResults(self::PAGE_SIZE);

        return $paginator;
    }
}
