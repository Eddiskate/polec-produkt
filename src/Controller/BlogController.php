<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/blog')]
class BlogController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $blogApiUrl,
        private readonly string $blogApiKey,
    ) {
    }

    #[Route('', name: 'app_blog_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->fetchPosts('/api/posts'),
        ]);
    }

    #[Route('/{slug}', name: 'app_blog_post', methods: ['GET'])]
    public function post(string $slug): Response
    {
        $post = $this->fetchPost($slug);
        if ($post === null) {
            throw $this->createNotFoundException('Post nie został znaleziony');
        }

        return $this->render('blog/post.html.twig', [
            'post' => $post,
            'body_html' => $this->bodyHtml($post),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchPosts(string $path): array
    {
        if ($this->blogApiUrl === '' || $this->blogApiKey === '') {
            return [];
        }

        try {
            $response = $this->httpClient->request('GET', rtrim($this->blogApiUrl, '/') . $path, [
                'headers' => [
                    'X-API-Key' => $this->blogApiKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $data = $response->toArray(false);
            $posts = $data['data'] ?? [];

            return is_array($posts) ? $posts : [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchPost(string $slug): ?array
    {
        if ($this->blogApiUrl === '' || $this->blogApiKey === '') {
            return null;
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                rtrim($this->blogApiUrl, '/') . '/api/posts/' . rawurlencode($slug),
                [
                    'headers' => [
                        'X-API-Key' => $this->blogApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => 10,
                ]
            );

            $data = $response->toArray(false);
            $post = $data['data'] ?? null;

            return is_array($post) ? $post : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $post
     */
    private function bodyHtml(array $post): string
    {
        $content = $post['content'] ?? $post['body'] ?? $post['html'] ?? '';
        if (is_array($content)) {
            return (string) ($content['html'] ?? '');
        }

        return (string) $content;
    }
}
