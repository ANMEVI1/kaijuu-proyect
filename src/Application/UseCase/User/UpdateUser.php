<?php
declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\UserRepositoryInterface;

class UpdateUser
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(string $id, array $data): void
    {
        $this->repository->update($id, $data);
    }
}
