<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\RechargePackageRepositoryInterface;

class GetRechargePackages {
    public function __construct(private RechargePackageRepositoryInterface $packageRepo) {}

    public function execute(): array {
        return $this->packageRepo->findAllActive();
    }
}
