<?php
declare(strict_types=1);

namespace App\Domain\Product;

interface ProductRepositoryInterface
{
    /** @return Product[] */
    public function findAll(): array;

    /** @return Product[] */
    public function findByCategory(string $categorySlug): array;

    public function findBySlug(string $slug): ?Product;
    public function findById(string $id): ?Product;

    public function search(string $query): array;
    
    public function create(array $data): ?Product;
    public function update(string $id, array $data): ?Product;
    public function delete(string $id): bool;
}
