# Deployment

This is a stateless Laravel front end with **no database**. It needs PHP and a
web server, and it talks to the Solar System DB REST API over HTTP. It runs
happily on a £5 VPS (Laravel Forge), a Cloudflare-fronted PHP host, or a
container — no provider-specific assumptions.

## Requirements

- PHP **8.4+** with the usual Laravel extensions (`mbstring`, `openssl`, `curl`, `dom`, …)
- Composer
- Node + npm — **build time only**, not at runtime
- A reachable Solar System DB API (`API_BASE_URL`)

## Environment

Copy `.env.example` to `.env` and set at least:

| Var            | Required | Notes                                                                 |
| -------------- | -------- | --------------------------------------------------------------------- |
| `APP_KEY`      | yes      | `php artisan key:generate`                                            |
| `APP_URL`      | yes      | Public URL — drives canonical URLs, OG tags, sitemap, JSON-LD         |
| `APP_ENV`      | yes      | `production`                                                          |
| `APP_DEBUG`    | yes      | `false` in production                                                 |
| `API_BASE_URL` | yes      | Backend REST root, e.g. `https://sol.wickedsick.com/api/v1`           |
| `SOLAR_API_TIMEOUT` | no  | HTTP timeout in seconds (default 8)                                   |
| `CACHE_STORE`  | no       | `file` is fine; `redis` recommended if available (better SWR)         |
| `SESSION_DRIVER` | no     | `file`                                                                |
| `QUEUE_CONNECTION` | no   | `sync` works; a real queue (`redis`/`database`) enables true background cache refresh |
| `CONTACT_EMAIL` | no      | Surfaced on `/about`                                                  |
| `API_DOCS_URL` | no       | Override the backend `/docs` link; otherwise derived from `API_BASE_URL` |

## Build & release

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan optimize          # config + route + view cache
```

If you change env or routes, re-run `php artisan optimize` (or
`php artisan optimize:clear` then `optimize`).

## Web server

Point the document root at `public/`. Standard Laravel rewrite to
`public/index.php`. HTTPS should terminate at the proxy/load balancer; the app
forces the `https` scheme for generated URLs in production.

## Caching & cache warming

All API responses are cached (see `config/services.php` → `solar.cache`). With a
real queue driver, stale entries refresh in the background so the cache never
goes cold. A nightly cron that warms the hot paths (the backend refreshes
nightly) keeps first-hit latency down:

```cron
# After the backend's nightly refresh — warm the homepage, listings and sitemap.
30 4 * * *  cd /path/to/app && php artisan cache:clear \
            && curl -s https://YOUR_DOMAIN/ > /dev/null \
            && curl -s https://YOUR_DOMAIN/objects > /dev/null \
            && curl -s https://YOUR_DOMAIN/sitemap.xml > /dev/null
```

If you use a queue for background cache refresh, run a worker:

```bash
php artisan queue:work --sleep=3 --tries=1
```

## Headers

`SetResponseHeaders` middleware adds baseline security headers to every
response (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`,
`Permissions-Policy`, `Cross-Origin-Opener-Policy`). HTML pages are left
`no-store` by Livewire (their state is server-rendered per request); the
sitemap and `robots.txt` send a public `Cache-Control`. If you want the CDN to
cache rendered HTML too, do it at the edge (e.g. a Cloudflare cache rule for
GET text/html), since those responses also carry a session cookie.

## Health & resilience

- If the backend is unreachable the site still serves every page (with a calm
  inline panel on the affected section) — it will not 500.
- `robots.txt` and `sitemap.xml` are generated dynamically; the sitemap is
  cached 24h.

## Post-deploy smoke check

```bash
curl -sI https://YOUR_DOMAIN/ | head -1                 # 200
curl -s  https://YOUR_DOMAIN/objects/planet-saturn | grep -o '<title>[^<]*'
curl -s  https://YOUR_DOMAIN/robots.txt | head -1
```
