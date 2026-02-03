# Laravel Reverb – local realtime (free, no limit)

Use Reverb for WebSockets on local (and in production on your own server). No per-connection or message limits.

## 1. Environment

In `.env` set:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

(Your `.env.example` already has these; copy them into `.env` if missing.)

- **Local:** `REVERB_HOST=localhost`, `REVERB_SCHEME=http`, `REVERB_PORT=8080`.
- **Production:** Use your real host (e.g. `reverb.yourapp.com`) and `REVERB_SCHEME=https` if you use TLS.

## 2. Start Reverb

In a **separate** terminal (keep it running):

```bash
php artisan reverb:start
```

You should see something like: `Reverb server started on 0.0.0.0:8080`.

## 3. Frontend (Vite)

So the browser gets the Reverb host/port/key:

- **Development:** run `npm run dev` (Vite injects `VITE_REVERB_*` from `.env`).
- **Production build:** run `npm run build` after changing any `VITE_*` or `REVERB_*` in `.env`.

## 4. Run the app

1. Terminal 1: `php artisan reverb:start`
2. Terminal 2: `php artisan serve` (and optionally `php artisan queue:work` if you use queues for broadcasts)
3. If using Vite: `npm run dev` (or use `npm run build` and serve built assets)

Open the app (e.g. `http://localhost:8000`). Club realtime (e.g. live scoreboard, lobby) will connect to `ws://localhost:8080` with no external service and no usage limits.

## Optional: run everything in one go

```bash
# Terminal 1
php artisan serve & php artisan reverb:start
```

Or use your existing `composer dev` script and add Reverb to it (e.g. with `concurrently`).

## Troubleshooting

- **“WebSocket connection failed”**  
  - Reverb must be running: `php artisan reverb:start`.  
  - `REVERB_HOST` / `REVERB_PORT` / `REVERB_SCHEME` in `.env` must match how the browser can reach the server (localhost for local).

- **No realtime updates**  
  - Confirm `BROADCAST_CONNECTION=reverb` in `.env`.  
  - Restart `php artisan reverb:start` and refresh the page (hard refresh if you changed Vite env: Ctrl+F5).

- **Private channels (e.g. club) not authorizing**  
  - User must be logged in.  
  - `routes/channels.php` must allow the channel (e.g. `club.{clubId}` for club members); your project already defines this.
