<?php
declare(strict_types=1);

namespace App\Domain\Product;

class Product
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly string  $description,
        public readonly float   $price,
        public readonly string  $imageUrl,
        public readonly string  $categoryId,
        public readonly int     $stock,
        public readonly string  $createdAt,
    ) {}

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
