<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Query\GetProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class GetProductsController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route(
        path: '/products/{page}',
        name: 'get_products',
        requirements: ['page' => Requirement::DIGITS],
        methods: ['GET'],
    )]
    public function __invoke(int $page = 1): JsonResponse
    {
        return $this->json($this->handle(new GetProducts($page)));
    }
}
