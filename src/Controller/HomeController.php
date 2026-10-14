<?php

namespace App\Controller;

use App\Service\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly HttpClientInterface $httpClient,
        private readonly string $blogApiUrl,
        private readonly string $blogApiKey,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'featured' => $this->products->featured(),
            'posts' => $this->fetchRecentPosts(3),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchRecentPosts(int $limit): array
    {
        if ($this->blogApiUrl === '' || $this->blogApiKey === '') {
            return [];
        }

        try {
            $response = $this->httpClient->request('GET', rtrim($this->blogApiUrl, '/') . '/api/posts', [
                'headers' => [
                    'X-API-Key' => $this->blogApiKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 8,
            ]);

            $data = $response->toArray(false);
            $posts = $data['data'] ?? [];

            return is_array($posts) ? array_slice($posts, 0, $limit) : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
