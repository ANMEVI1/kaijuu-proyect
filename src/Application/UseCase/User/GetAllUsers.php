<?php
declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\UserRepositoryInterface;

class GetAllUsers
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
