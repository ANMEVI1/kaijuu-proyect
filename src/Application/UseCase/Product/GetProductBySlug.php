<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\ProductRepositoryInterface;

class GetProductBySlug
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function execute(string $slug): ?\App\Domain\Product\Product
    {
        return $this->repo->findBySlug($slug);
    }
}
