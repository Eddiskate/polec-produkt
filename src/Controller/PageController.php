<?php

namespace App\Controller;

use App\Service\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $blogApiUrl = '',
        private readonly string $blogApiKey = '',
    ) {
    }

    #[Route('/o-nas', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }

    #[Route('/zglos-produkt', name: 'app_submit_product')]
    public function submitProduct(): Response
    {
        return $this->render('page/submit.html.twig');
    }

    #[Route('/robots.txt', name: 'app_robots', priority: 10)]
    public function robots(): Response
    {
        $content = "User-agent: *\nAllow: /\nSitemap: https://polec-produkt.pl/sitemap.xml\n";

        return new Response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', priority: 10)]
    public function sitemap(ProductRepository $products): Response
    {
        $entries = [
            ['loc' => '/', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => '/produkty', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => '/blog', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => '/o-nas', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/zglos-produkt', 'changefreq' => 'monthly', 'priority' => '0.4'],
        ];

        foreach ($products->all() as $product) {
            $slug = $product['slug'] ?? '';
            if (!is_string($slug) || $slug === '') {
                continue;
            }
            $entries[] = [
                'loc' => '/produkty/' . $slug,
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        foreach ($this->fetchBlogSlugs() as $slug) {
            $entries[] = [
                'loc' => '/blog/' . $slug,
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($entries as $entry) {
            $loc = 'https://polec-produkt.pl' . $entry['loc'];
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
            $xml .= '    <changefreq>' . $entry['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $entry['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * @return list<string>
     */
    private function fetchBlogSlugs(): array
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
            if (!is_array($posts)) {
                return [];
            }

            $slugs = [];
            foreach ($posts as $post) {
                if (!is_array($post)) {
                    continue;
                }
                $slug = $post['slug'] ?? null;
                if (is_string($slug) && $slug !== '') {
                    $slugs[] = $slug;
                }
            }

            return $slugs;
        } catch (\Throwable) {
            return [];
        }
    }
}
