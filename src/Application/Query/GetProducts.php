<?php

declare(strict_types=1);

namespace App\Application\Query;

readonly class GetProducts
{
    public function __construct(public int $page)
    {
    }
}
