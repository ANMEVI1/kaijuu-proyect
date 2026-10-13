<?php
declare(strict_types=1);

namespace App\Domain\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function findAll(): array;
    public function findBySlug(string $slug): ?Category;
    public function findById(string $id): ?Category;
    public function create(array $data): Category;
    public function update(string $id, array $data): void;
    public function delete(string $id): void;
}
