<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;

class CreateProduct
{
    public function __construct(private ProductRepositoryInterface $repository) {}

    public function execute(array $data): ?Product
    {
        return $this->repository->create($data);
    }
}
