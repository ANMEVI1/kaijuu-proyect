<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;

class GetProductById
{
    public function __construct(private readonly ProductRepositoryInterface $repo) {}

    public function execute(string $id): ?Product
    {
        return $this->repo->findById($id);
    }
}
