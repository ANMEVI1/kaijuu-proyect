<?php
declare(strict_types=1);

namespace App\Domain\User;

class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $name,
        public readonly ?string $phone,
        public readonly ?string $avatarUrl,
        public readonly string $role,
        public readonly string $createdAt
    ) {}
}
