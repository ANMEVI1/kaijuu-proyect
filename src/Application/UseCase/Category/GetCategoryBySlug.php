<?php
declare(strict_types=1);

namespace App\Application\UseCase\Category;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepositoryInterface;

class GetCategoryBySlug
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function execute(string $slug): ?Category
    {
        return $this->repository->findBySlug($slug);
    }
}
