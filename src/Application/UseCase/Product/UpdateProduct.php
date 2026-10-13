<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;

class UpdateProduct
{
    public function __construct(private ProductRepositoryInterface $repository) {}

    public function execute(string $id, array $data): ?Product
    {
        return $this->repository->update($id, $data);
    }
}
