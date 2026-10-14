<?php

namespace App\Controller;

use App\Service\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
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

    #[Route('/robots.txt', name: 'app_robots')]
    public function robots(): Response
    {
        $content = "User-agent: *\nAllow: /\nSitemap: https://polec-produkt.pl/sitemap.xml\n";

        return new Response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    #[Route('/sitemap.xml', name: 'app_sitemap')]
    public function sitemap(ProductRepository $products): Response
    {
        $urls = ['/', '/produkty', '/blog', '/o-nas', '/zglos-produkt'];
        foreach ($products->all() as $product) {
            $urls[] = '/produkty/' . ($product['slug'] ?? '');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $path) {
            $xml .= '  <url><loc>https://polec-produkt.pl' . htmlspecialchars($path, ENT_XML1) . '</loc></url>' . "\n";
        }
        $xml .= '</urlset>';

        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
