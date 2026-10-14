<?php

namespace App\Controller;

use App\Service\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
    ) {
    }

    #[Route('/produkty', name: 'app_products')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'grouped' => $this->products->groupedByCategory(),
            'products' => $this->products->all(),
        ]);
    }

    #[Route('/produkty/{slug}', name: 'app_product_show')]
    public function show(string $slug): Response
    {
        $product = $this->products->findBySlug($slug);
        if ($product === null) {
            throw $this->createNotFoundException('Produkt nie został znaleziony');
        }

        $related = array_values(array_filter(
            $this->products->all(),
            static fn (array $p): bool => ($p['slug'] ?? '') !== $slug
                && ($p['category'] ?? '') === ($product['category'] ?? '')
        ));

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'related' => array_slice($related, 0, 3),
        ]);
    }
}
