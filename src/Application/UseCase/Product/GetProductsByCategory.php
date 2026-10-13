<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\ProductRepositoryInterface;

class GetProductsByCategory
{
    public function __construct(private ProductRepositoryInterface $repository) {}

    public function execute(string $categorySlug): array
    {
        return $this->repository->findByCategory($categorySlug);
    }
}
