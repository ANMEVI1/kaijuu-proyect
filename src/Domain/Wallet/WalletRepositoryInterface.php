<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

interface WalletRepositoryInterface {
    public function findByUserId(string $userId): ?Wallet;
    public function createForUser(string $userId): Wallet;
    public function credit(string $walletId, float $amount): Wallet;   // suma ZEN
    public function debit(string $walletId, float $amount): Wallet;    // resta ZEN
    public function getTransactions(string $walletId, int $limit = 20): array;
    public function recordTransaction(array $data): WalletTransaction;
    public function findTransactionByReferenceCode(string $code): ?WalletTransaction;
    public function updateTransactionStatus(string $transactionId, string $status): void;
}
