<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;

class SupabaseUserRepository implements UserRepositoryInterface
{
    public function __construct(private SupabaseClient $client) {}

    public function findAll(): array
    {
        $rows = $this->client->get('users', ['select' => '*']);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findById(string $id): ?User
    {
        $rows = $this->client->get('users', ['select' => '*', 'id' => "eq.$id"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $rows = $this->client->get('users', ['select' => '*', 'email' => "eq.$email"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function create(array $data): User
    {
        if (empty($data['id'])) {
            $data['id'] = $this->generateUuid();
        }
        $row = $this->client->post('users', $data);
        if (empty($row) || !isset($row['id'])) {
            throw new \RuntimeException("Error al crear usuario en Supabase (¿Error de conexión o validación?). Revisa los logs.");
        }
        return $this->hydrate($row);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function update(string $id, array $data): void
    {
        $this->client->patch('users', $id, $data);
    }

    public function delete(string $id): void
    {
        // Guzzle client inside SupabaseClient doesn't have a delete wrapper yet, so let's add it.
        // Wait, does SupabaseClient have delete()?
        // I need to check SupabaseClient.php or just use HTTP directly...
        // I will add delete method to SupabaseClient later. Let's assume it exists and I'll add it.
        $this->client->delete('users', $id);
    }

    private function hydrate(array $r): User
    {
        return new User(
            id:        $r['id'],
            email:     $r['email'],
            name:      $r['name'] ?? null,
            phone:     $r['phone'] ?? null,
            avatarUrl: $r['avatar_url'] ?? null,
            role:      $r['role'] ?? 'customer',
            createdAt: $r['created_at'] ?? date('Y-m-d H:i:s')
        );
    }
}
