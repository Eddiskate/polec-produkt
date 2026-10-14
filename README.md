# polec-produkt.pl

Katalog poleceń (hosting, narzędzia, później inne kategorie z afiliacją). Symfony 6.4 + blog z API web-portalu.

## Lokalnie

```bash
composer install
cp .env.example .env.local   # uzupełnij sekrety
php -S 127.0.0.1:8000 -t public
```

## EasyPanel

Zobacz [EASYPANEL.md](EASYPANEL.md).

## Produkty

Edytuj [`config/products.yaml`](config/products.yaml) — po rejestracji w programach partnerskich wklej prawdziwe `affiliateUrl`.
