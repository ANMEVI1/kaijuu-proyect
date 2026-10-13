<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;

class SupabaseProductRepository implements ProductRepositoryInterface
{
    public function __construct(private SupabaseClient $client) {}

    public function findAll(): array
    {
        $rows = $this->client->get('products', ['select' => '*', 'order' => 'created_at.desc']);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findByCategory(string $categorySlug): array
    {
        $catRows = $this->client->get('categories', [
            'select' => 'id',
            'slug'   => "eq.$categorySlug"
        ]);

        if (empty($catRows)) {
            return [];
        }

        $catId = $catRows[0]['id'];
        $rows = $this->client->get('products', [
            'select'      => '*',
            'category_id' => "eq.$catId",
            'order'       => 'created_at.desc'
        ]);

        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findBySlug(string $slug): ?Product
    {
        $rows = $this->client->get('products', ['select' => '*', 'slug' => "eq.$slug"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function findById(string $id): ?Product
    {
        $rows = $this->client->get('products', ['select' => '*', 'id' => "eq.$id"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function search(string $query): array
    {
        $rows = $this->client->get('products', [
            'select' => '*',
            'name'   => "ilike.*$query*",
        ]);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function create(array $data): ?Product
    {
        $result = $this->client->post('products', $data);
        if (empty($result) || !isset($result['id'])) {
            return null;
        }
        return $this->hydrate($result);
    }

    public function update(string $id, array $data): ?Product
    {
        $result = $this->client->patch('products', $id, $data);
        if (empty($result) || !isset($result['id'])) {
            return $this->findById($id);
        }
        return $this->hydrate($result);
    }

    public function delete(string $id): bool
    {
        $this->client->delete('products', $id);
        return true;
    }

    private function hydrate(array $r): Product
    {
        return new Product(
            id:          $r['id'],
            name:        $r['name'],
            slug:        $r['slug'],
            description: $r['description'] ?? '',
            price:       (float)$r['price'],
            imageUrl:    $r['image_url'] ?? '',
            categoryId:  $r['category_id'] ?? '',
            stock:       (int)$r['stock'],
            createdAt:   $r['created_at'],
        );
    }
}
