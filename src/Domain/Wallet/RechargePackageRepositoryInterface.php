<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

interface RechargePackageRepositoryInterface {
    public function findAllActive(): array;
    public function findById(string $id): ?RechargePackage;
    public function create(array $data): RechargePackage;
    public function update(string $id, array $data): void;
    public function delete(string $id): void;
}
