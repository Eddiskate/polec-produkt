<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly string $contactEmail = 'kontakt@polec-produkt.pl',
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'contact_email' => $this->contactEmail,
            'site_name' => 'Poleć Produkt',
            'site_url' => 'https://polec-produkt.pl',
        ];
    }
}
