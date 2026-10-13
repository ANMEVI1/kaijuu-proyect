<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\ProductRepositoryInterface;

class DeleteProduct
{
    public function __construct(private ProductRepositoryInterface $repository) {}

    public function execute(string $id): bool
    {
        return $this->repository->delete($id);
    }
}
