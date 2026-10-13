<?php
declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Domain\Product\ProductRepositoryInterface;

class SearchProducts
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function execute(string $query): array
    {
        return $this->repo->search($query);
    }
}
