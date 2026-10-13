<?php
declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\UserRepositoryInterface;

class DeleteUser
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(string $id): void
    {
        $this->repository->delete($id);
    }
}
