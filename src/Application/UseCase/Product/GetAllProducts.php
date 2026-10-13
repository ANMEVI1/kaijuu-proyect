<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\ProductRepositoryInterface;

class GetAllProducts
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function execute(): array
    {
        return $this->repo->findAll();
    }
}
