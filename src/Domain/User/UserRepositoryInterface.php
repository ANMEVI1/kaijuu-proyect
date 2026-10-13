<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepositoryInterface
{
    /** @return User[] */
    public function findAll(): array;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function update(string $id, array $data): void;
    public function delete(string $id): void;
}
