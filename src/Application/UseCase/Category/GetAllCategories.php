<?php
declare(strict_types=1);

namespace App\Application\UseCase\Category;

use App\Domain\Category\CategoryRepositoryInterface;

class GetAllCategories
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
