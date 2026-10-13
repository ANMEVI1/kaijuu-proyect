<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepositoryInterface;

class SupabaseCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private SupabaseClient $client) {}

    public function findAll(): array
    {
        $rows = $this->client->get('categories', ['select' => '*']);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findBySlug(string $slug): ?Category
    {
        $rows = $this->client->get('categories', [
            'select' => '*',
            'slug'   => "eq.$slug"
        ]);

        if (empty($rows)) {
            return null;
        }

        return $this->hydrate($rows[0]);
    }

    public function findById(string $id): ?Category
    {
        $rows = $this->client->get('categories', ['select' => '*', 'id' => "eq.$id"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function create(array $data): Category
    {
        $row = $this->client->post('categories', $data);
        if (empty($row) || !isset($row['id'])) {
            throw new \RuntimeException("Error al crear categoría en Supabase");
        }
        return $this->hydrate($row);
    }

    public function update(string $id, array $data): void
    {
        $this->client->patch('categories', $id, $data);
    }

    public function delete(string $id): void
    {
        $this->client->delete('categories', $id);
    }

    private function hydrate(array $row): Category
    {
        return new Category(
            id: $row['id'],
            name: $row['name'],
            slug: $row['slug']
        );
    }
}
