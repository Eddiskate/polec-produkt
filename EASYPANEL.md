# EasyPanel — polec-produkt.pl

## Nowy serwis w EasyPanel

1. **Create App** → typ **App** (Docker).
2. **Source**: Git repo z tym projektem (branch `main`).
3. **Build**: Dockerfile (ścieżka: `./Dockerfile`).
4. **Port**: `80` (kontener słucha na 80; EasyPanel wystawi domenę).
5. **Domain**: `polec-produkt.pl` (+ `www` z redirectem jeśli chcesz).

## Environment (Environment Variables)

Ustaw w panelu (nie commituj sekretów):

| Key | Przykład |
|-----|----------|
| `APP_ENV` | `prod` |
| `APP_SECRET` | długi losowy string |
| `APP_DEBUG` | `0` |
| `BLOG_API_URL` | `https://web-portal.pl` |
| `BLOG_API_KEY` | klucz z web-portal (jak BlackPage) |
| `CONTACT_EMAIL` | `kontakt@polec-produkt.pl` |

## Po deployu

1. Otwórz `https://polec-produkt.pl` — home + produkty.
2. W `config/products.yaml` podmień `affiliateUrl` na prawdziwe reflinki po rejestracji w programach (dhosting, home.pl, VH, LH, cyber_Folks).
3. DNS: A/CNAME domeny na IP / target EasyPanel.

## Lokalnie

```bash
composer install
# skopiuj .env.example → .env.local i uzupełnij
symfony server:start
# albo: php -S 127.0.0.1:8000 -t public
```

## Afiliacja (poza kodem)

- Zarejestruj się w programach partnerskich hostów.
- Wklej reflinki do `affiliateUrl` w `config/products.yaml` i zredeployuj.
