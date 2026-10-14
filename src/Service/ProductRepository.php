<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class ProductRepository
{
    /** @var list<array<string, mixed>>|null */
    private ?array $products = null;

    public function __construct(
        private readonly string $productsFile,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        return $this->load();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function featured(): array
    {
        return array_values(array_filter(
            $this->load(),
            static fn (array $p): bool => !empty($p['featured'])
        ));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        foreach ($this->load() as $product) {
            if (($product['slug'] ?? '') === $slug) {
                return $product;
            }
        }

        return null;
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function groupedByCategory(): array
    {
        $grouped = [];
        foreach ($this->load() as $product) {
            $key = (string) ($product['category'] ?? 'inne');
            $grouped[$key][] = $product;
        }

        return $grouped;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function load(): array
    {
        if ($this->products !== null) {
            return $this->products;
        }

        if (!is_file($this->productsFile)) {
            return $this->products = [];
        }

        $data = Yaml::parseFile($this->productsFile);
        $list = $data['products'] ?? [];

        return $this->products = is_array($list) ? array_values($list) : [];
    }
}
