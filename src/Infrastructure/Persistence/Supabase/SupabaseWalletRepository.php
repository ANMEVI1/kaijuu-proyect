<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletTransaction;
use App\Domain\Wallet\WalletRepositoryInterface;

class SupabaseWalletRepository implements WalletRepositoryInterface {
    public function __construct(private SupabaseClient $client) {}

    public function findByUserId(string $userId): ?Wallet {
        $rows = $this->client->get('wallets', ['select' => '*', 'user_id' => "eq.$userId"]);
        return isset($rows[0]) ? $this->hydrateWallet($rows[0]) : null;
    }

    public function createForUser(string $userId): Wallet {
        $row = $this->client->post('wallets', ['user_id' => $userId, 'balance_zen' => 0.00]);
        if (empty($row) || !isset($row['id'])) {
            throw new \RuntimeException("Error creando monedero");
        }
        return $this->hydrateWallet($row);
    }

    public function credit(string $walletId, float $amount): Wallet {
        // In a real app we'd use a postgres RPC function to prevent race conditions.
        // For now, we fetch, calculate and patch.
        $rows = $this->client->get('wallets', ['select' => '*', 'id' => "eq.$walletId"]);
        if (empty($rows)) throw new \RuntimeException("Wallet no encontrado");
        $wallet = $rows[0];
        $newBalance = (float)$wallet['balance_zen'] + $amount;
        $this->client->patch('wallets', $walletId, ['balance_zen' => $newBalance, 'updated_at' => gmdate('c')]);
        
        $wallet['balance_zen'] = $newBalance;
        return $this->hydrateWallet($wallet);
    }

    public function debit(string $walletId, float $amount): Wallet {
        $rows = $this->client->get('wallets', ['select' => '*', 'id' => "eq.$walletId"]);
        if (empty($rows)) throw new \RuntimeException("Wallet no encontrado");
        $wallet = $rows[0];
        $newBalance = (float)$wallet['balance_zen'] - $amount;
        if ($newBalance < 0) throw new \RuntimeException("Saldo insuficiente en monedero");
        
        $this->client->patch('wallets', $walletId, ['balance_zen' => $newBalance, 'updated_at' => gmdate('c')]);
        
        $wallet['balance_zen'] = $newBalance;
        return $this->hydrateWallet($wallet);
    }

    public function getTransactions(string $walletId, int $limit = 20): array {
        $rows = $this->client->get('wallet_transactions', [
            'wallet_id' => "eq.$walletId",
            'order' => 'created_at.desc',
            'limit' => $limit
        ]);
        return array_map(fn($r) => $this->hydrateTransaction($r), $rows);
    }

    public function recordTransaction(array $data): WalletTransaction {
        $row = $this->client->post('wallet_transactions', $data);
        if (empty($row) || !isset($row['id'])) {
            throw new \RuntimeException("Error al grabar transacción");
        }
        return $this->hydrateTransaction($row);
    }

    public function findTransactionByReferenceCode(string $code): ?WalletTransaction {
        $rows = $this->client->get('wallet_transactions', ['select' => '*', 'reference_code' => "eq.$code"]);
        return isset($rows[0]) ? $this->hydrateTransaction($rows[0]) : null;
    }

    public function updateTransactionStatus(string $transactionId, string $status): void {
        $this->client->patch('wallet_transactions', $transactionId, ['status' => $status]);
    }

    private function hydrateWallet(array $r): Wallet {
        return new Wallet(
            id: $r['id'],
            userId: $r['user_id'],
            balanceZen: (float)$r['balance_zen'],
            createdAt: $r['created_at'],
            updatedAt: $r['updated_at']
        );
    }

    private function hydrateTransaction(array $r): WalletTransaction {
        return new WalletTransaction(
            id: $r['id'],
            walletId: $r['wallet_id'],
            userId: $r['user_id'],
            type: $r['type'],
            amountZen: (float)$r['amount_zen'],
            direction: $r['direction'],
            balanceBefore: (float)$r['balance_before'],
            balanceAfter: (float)$r['balance_after'],
            exchangeRateId: $r['exchange_rate_id'] ?? null,
            amountFiat: isset($r['amount_fiat']) ? (float)$r['amount_fiat'] : null,
            currency: $r['currency'] ?? 'PEN',
            orderId: $r['order_id'] ?? null,
            referenceCode: $r['reference_code'] ?? null,
            status: $r['status'],
            notes: $r['notes'] ?? null,
            createdAt: $r['created_at']
        );
    }
}
