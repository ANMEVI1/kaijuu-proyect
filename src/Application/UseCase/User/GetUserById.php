<?php
declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;

class GetUserById
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(string $id): ?User
    {
        return $this->repository->findById($id);
    }
}
