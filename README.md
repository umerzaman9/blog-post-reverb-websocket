# Websocket Reverb — Real-Time Blog Comments

A small Laravel 10 blog application built to demonstrate real-time comment broadcasting using **Laravel Reverb** and **Laravel Echo**. Guests and authenticated users alike can watch comments appear live on a post page, with no page refresh required.

## Features

- Blog posts (seeded via factory — no CRUD UI)
- Real-time comments on each post, broadcast over WebSockets via Laravel Reverb
- Guests can view posts and comments live; only authenticated users can post comments
- Authentication via Laravel Breeze (Blade, session-based)
- Repository pattern — controllers stay thin, all business logic and formatted responses live in repositories
- API Resources for consistent JSON formatting
- Form Requests for validation
- Toast notifications via PHPFlasher (Toastr adapter)
- Bootstrap 5 UI (no Tailwind/Vue)

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 10, PHP 8.2 |
| Real-time | Laravel Reverb (WebSocket server), Laravel Echo, Pusher JS protocol |
| Auth | Laravel Breeze (Blade stack) |
| Frontend | Blade, Bootstrap 5, JQuery |
| Notifications | PHPFlasher + Toastr |
| Database | MySQL |

## Requirements

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL
- Laragon (or any local dev environment capable of serving a custom `.test`/`.local` domain)

## Installation

```bash
# Clone and install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials and Reverb settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=sync

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

If Reverb hasn't been installed/configured yet:

```bash
composer require laravel/reverb
php artisan reverb:install
```

## Database Setup

```bash
php artisan migrate:fresh --seed
```

This creates:
- A test user: `test@example.com` / `password`
- 10 seeded blog posts owned by that user

## Notifications (PHPFlasher)

```bash
php artisan flasher:install
```

This publishes the required JS/CSS assets to `public/vendor/flasher`. Assets are auto-injected into every page automatically — no manual `<script>` tags needed.

## Build Frontend Assets

```bash
npm run build
```

> **Note:** This project does not use `npm run dev` due to CORS conflicts between the Vite dev server and custom `.test` domains under Laragon. Re-run `npm run build` any time you change a file under `resources/js/`.

## Running the App

Three processes need to run simultaneously, each in its own terminal:

```bash
# 1. Your web server (Laragon serves this automatically, or run manually:)
php artisan serve

# 2. The Reverb WebSocket server
php artisan reverb:start

# 3. (Only if you change frontend assets)
npm run build
```

Then visit your configured domain (e.g. `http://websocket-reverb.test`) or `http://localhost:8000` if using `artisan serve`.

## How Real-Time Comments Work

1. A user submits a comment via `fetch()` on the post page.
2. The request hits `CommentController@store`, validated by `StoreCommentRequest`.
3. `CommentRepository@store` creates the comment, then fires the `CommentPosted` event.
4. `CommentPosted` broadcasts on a **public** channel (`posts.{postId}`) — no channel authorization needed, so guests can listen too.
5. Laravel Reverb pushes the event to every connected client subscribed to that channel.
6. `resources/js/comments.js` (loaded via Echo) listens for `.comment.posted` and appends the new comment to the DOM instantly — for every tab/user watching that post, without a page refresh.
7. `->toOthers()` combined with the `X-Socket-Id` header (sent manually since `fetch()` doesn't attach it automatically like `axios` does) ensures the comment's author doesn't see a duplicate — their own comment renders immediately from the direct API response instead.

## Notes on Design Decisions

- **Comment routes live in `routes/web.php`, not `routes/api.php`** — despite returning JSON, they rely on Laravel's session-based auth and CSRF protection (via the `web` middleware group), which `routes/api.php` does not provide by default.
- **Public (not private) broadcast channel** — required so guests can watch comments live without needing to authenticate against a channel.
- **`QUEUE_CONNECTION=sync`** — broadcasting events are dispatched through the queue by default; without a queue worker running, `sync` processes them inline immediately, which is simplest for local development.

## Known Limitations

- No post creation/editing UI — posts are seed-only by design for this demo
- No pagination on the post listing (fine for the current seed size of 10)
- No rate limiting on comment submission
