<?php
declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;

class CreateUser
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(array $data): User
    {
        return $this->repository->create($data);
    }
}
